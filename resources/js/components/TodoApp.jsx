import React, { useState, useEffect } from 'react';

/**
 * Componente principal da aplicação Todo List
 * 
 * Este componente gerencia todo o estado e funcionalidades da aplicação:
 * - Lista de tarefas
 * - Adicionar novas tarefas
 * - Marcar/desmarcar tarefas como concluídas
 * - Deletar tarefas
 * - Sincronização com API Laravel e fallback para localStorage
 */
const TodoApp = () => {
    // Estado para armazenar a lista de tarefas
    const [todos, setTodos] = useState([]);
    // Estado para controlar o valor do input de nova tarefa
    const [newTodo, setNewTodo] = useState('');
    // Estado para indicar quando está carregando dados da API
    const [loading, setLoading] = useState(false);

    /**
     * Carrega as tarefas da API Laravel
     * Se a API não estiver disponível, carrega do localStorage como fallback
     */
    const fetchTodos = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/todos');
            if (response.ok) {
                const data = await response.json();
                setTodos(data);
            }
        } catch (error) {
            console.error('Erro ao carregar tarefas:', error);
            // Fallback: carrega tarefas do localStorage se a API falhar
            const savedTodos = localStorage.getItem('todos');
            if (savedTodos) {
                setTodos(JSON.parse(savedTodos));
            }
        } finally {
            setLoading(false);
        }
    };

    /**
     * Adiciona uma nova tarefa via API Laravel
     * @param {Object} todoData - Dados da tarefa (text, completed)
     * @returns {boolean} - True se sucesso, false se falhar
     */
    const addTodoAPI = async (todoData) => {
        try {
            const response = await fetch('/api/todos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Token CSRF para proteção contra ataques
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(todoData)
            });
            
            if (response.ok) {
                const newTodo = await response.json();
                // Adiciona a nova tarefa no início da lista
                setTodos(prev => [newTodo, ...prev]);
                return true;
            }
        } catch (error) {
            console.error('Erro ao adicionar tarefa:', error);
        }
        return false;
    };

    /**
     * Atualiza uma tarefa existente via API Laravel
     * @param {number} id - ID da tarefa
     * @param {Object} updates - Campos a serem atualizados
     * @returns {boolean} - True se sucesso, false se falhar
     */
    const updateTodoAPI = async (id, updates) => {
        try {
            const response = await fetch(`/api/todos/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(updates)
            });
            
            if (response.ok) {
                const updatedTodo = await response.json();
                // Atualiza a tarefa na lista local
                setTodos(prev => prev.map(todo => 
                    todo.id === id ? updatedTodo : todo
                ));
                return true;
            }
        } catch (error) {
            console.error('Erro ao atualizar tarefa:', error);
        }
        return false;
    };

    /**
     * Remove uma tarefa via API Laravel
     * @param {number} id - ID da tarefa a ser removida
     * @returns {boolean} - True se sucesso, false se falhar
     */
    const deleteTodoAPI = async (id) => {
        try {
            const response = await fetch(`/api/todos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (response.ok) {
                // Remove a tarefa da lista local
                setTodos(prev => prev.filter(todo => todo.id !== id));
                return true;
            }
        } catch (error) {
            console.error('Erro ao deletar tarefa:', error);
        }
        return false;
    };

    // Carrega as tarefas quando o componente é montado pela primeira vez
    useEffect(() => {
        fetchTodos();
    }, []);

    // Salva as tarefas no localStorage sempre que a lista mudar
    // Isso serve como backup caso a API não esteja disponível
    useEffect(() => {
        localStorage.setItem('todos', JSON.stringify(todos));
    }, [todos]);

    /**
     * Adiciona uma nova tarefa
     * Tenta usar a API primeiro, se falhar usa localStorage
     */
    const addTodo = async (e) => {
        e.preventDefault();
        if (newTodo.trim() === '') return;

        const todoData = {
            text: newTodo.trim(),
            completed: false
        };

        // Tenta adicionar via API primeiro
        const success = await addTodoAPI(todoData);
        
        if (!success) {
            // Fallback: adiciona localmente se a API falhar
            const todo = {
                id: Date.now(), // Usa timestamp como ID temporário
                text: newTodo.trim(),
                completed: false,
                created_at: new Date().toISOString()
            };
            setTodos(prev => [todo, ...prev]);
        }
        
        // Limpa o input após adicionar
        setNewTodo('');
    };

    /**
     * Alterna o status de conclusão de uma tarefa
     * @param {number} id - ID da tarefa
     */
    const toggleTodo = async (id) => {
        const todo = todos.find(t => t.id === id);
        if (!todo) return;

        const updates = { completed: !todo.completed };
        
        // Tenta atualizar via API primeiro
        const success = await updateTodoAPI(id, updates);
        
        if (!success) {
            // Fallback: atualiza localmente se a API falhar
            setTodos(prev => prev.map(todo =>
                todo.id === id ? { ...todo, completed: !todo.completed } : todo
            ));
        }
    };

    /**
     * Remove uma tarefa da lista
     * @param {number} id - ID da tarefa a ser removida
     */
    const deleteTodo = async (id) => {
        // Tenta deletar via API primeiro
        const success = await deleteTodoAPI(id);
        
        if (!success) {
            // Fallback: remove localmente se a API falhar
            setTodos(prev => prev.filter(todo => todo.id !== id));
        }
    };

    return (
        <div className="min-h-screen bg-gray-100 py-8">
            <div className="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
                {/* Título da aplicação */}
                <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">
                    📝 Todo List
                </h1>

                {/* Formulário para adicionar nova tarefa */}
                <form onSubmit={addTodo} className="mb-6">
                    <div className="flex gap-2">
                        <input
                            type="text"
                            value={newTodo}
                            onChange={(e) => setNewTodo(e.target.value)}
                            placeholder="Adicionar nova tarefa..."
                            className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <button
                            type="submit"
                            className="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Adicionar
                        </button>
                    </div>
                </form>

                {/* Lista de tarefas */}
                <div className="space-y-3">
                    {loading ? (
                        <p className="text-center text-gray-500">Carregando tarefas...</p>
                    ) : todos.length === 0 ? (
                        <p className="text-center text-gray-500">Nenhuma tarefa ainda. Adicione uma nova tarefa!</p>
                    ) : (
                        todos.map(todo => (
                            <div
                                key={todo.id}
                                className={`flex items-center justify-between p-4 border rounded-lg ${
                                    todo.completed ? 'bg-gray-50 border-gray-200' : 'bg-white border-gray-300'
                                }`}
                            >
                                <div className="flex items-center gap-3">
                                    {/* Checkbox para marcar como concluída */}
                                    <input
                                        type="checkbox"
                                        checked={todo.completed}
                                        onChange={() => toggleTodo(todo.id)}
                                        className="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                    />
                                    {/* Texto da tarefa */}
                                    <span
                                        className={`${
                                            todo.completed ? 'line-through text-gray-500' : 'text-gray-800'
                                        }`}
                                    >
                                        {todo.text}
                                    </span>
                                </div>
                                {/* Botão para deletar tarefa */}
                                <button
                                    onClick={() => deleteTodo(todo.id)}
                                    className="text-red-500 hover:text-red-700 focus:outline-none"
                                >
                                    🗑️
                                </button>
                            </div>
                        ))
                    )}
                </div>

                {/* Estatísticas das tarefas */}
                {todos.length > 0 && (
                    <div className="mt-6 pt-4 border-t border-gray-200">
                        <p className="text-sm text-gray-600 text-center">
                            {todos.filter(todo => todo.completed).length} de {todos.length} tarefas concluídas
                        </p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TodoApp; 
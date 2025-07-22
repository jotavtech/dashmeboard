<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador responsável por gerenciar as operações CRUD das tarefas
 * Fornece endpoints da API para criar, ler, atualizar e deletar tarefas
 */
class TodoController extends Controller
{
    /**
     * Lista todas as tarefas do usuário logado ordenadas por data de criação (mais recentes primeiro)
     * Retorna um JSON com todas as tarefas do usuário autenticado
     */
    public function index(): JsonResponse
    {
        // Busca todas as tarefas do usuário logado ordenadas por data de criação decrescente
        $todos = Todo::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($todos);
    }

    /**
     * Cria uma nova tarefa no sistema para o usuário logado
     * Valida os dados de entrada e salva a tarefa no banco de dados
     */
    public function store(Request $request): JsonResponse
    {
        // Valida se o texto da tarefa foi fornecido e tem no máximo 255 caracteres
        $request->validate([
            'text' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        // Cria uma nova tarefa com status "não concluída" por padrão e vincula ao usuário logado
        $todo = Todo::create([
            'text' => $request->text,
            'completed' => false,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'project_id' => $request->project_id,
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'description' => $request->description
        ]);

        // Retorna a tarefa criada com status HTTP 201 (Created)
        return response()->json($todo, 201);
    }

    /**
     * Busca e retorna uma tarefa específica pelo ID (apenas do usuário logado)
     * Se a tarefa não existir ou não pertencer ao usuário, retorna erro 404
     */
    public function show(string $id): JsonResponse
    {
        // Busca a tarefa pelo ID e do usuário logado ou retorna erro 404 se não encontrar
        $todo = Todo::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return response()->json($todo);
    }

    /**
     * Atualiza uma tarefa existente do usuário logado
     * Permite atualizar o texto e/ou o status de conclusão da tarefa
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Valida os dados opcionais (texto e status de conclusão)
        $request->validate([
            'text' => 'sometimes|required|string|max:255',
            'completed' => 'sometimes|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        // Busca a tarefa pelo ID e do usuário logado ou retorna erro 404 se não encontrar
        $todo = Todo::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        // Atualiza apenas os campos fornecidos na requisição
        $todo->update($request->only([
            'text', 'completed', 'category_id', 'project_id', 
            'priority', 'due_date', 'description'
        ]));

        return response()->json($todo);
    }

    /**
     * Remove uma tarefa do usuário logado do sistema
     * Deleta permanentemente a tarefa do banco de dados
     */
    public function destroy(string $id): JsonResponse
    {
        // Busca a tarefa pelo ID e do usuário logado ou retorna erro 404 se não encontrar
        $todo = Todo::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        // Remove a tarefa do banco de dados
        $todo->delete();

        // Retorna mensagem de sucesso
        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}

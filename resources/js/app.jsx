import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import TodoApp from './components/TodoApp';

/**
 * Arquivo principal da aplicação React
 * 
 * Este arquivo é responsável por inicializar e renderizar a aplicação React
 * no elemento HTML com id 'app' definido na view Blade.
 */

// Cria a raiz do React no elemento DOM com id 'app'
const root = ReactDOM.createRoot(document.getElementById('app'));

// Renderiza o componente TodoApp dentro do StrictMode do React
// StrictMode ajuda a identificar problemas potenciais durante o desenvolvimento
root.render(
    <React.StrictMode>
        <TodoApp />
    </React.StrictMode>
);

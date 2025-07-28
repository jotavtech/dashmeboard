<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AtividadeController;

/**
 * Rotas da API
 * 
 * Aqui são definidas todas as rotas da API da aplicação.
 * Estas rotas são carregadas pelo RouteServiceProvider e
 * todas recebem o middleware "api" automaticamente.
 */

// Rota para obter informações do usuário autenticado (não usada na aplicação atual)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Rotas da API para gerenciar tarefas (CRUD completo)
 * 
 * GET    /api/todos      - Lista todas as tarefas
 * POST   /api/todos      - Cria uma nova tarefa
 * GET    /api/todos/{id} - Mostra uma tarefa específica
 * PUT    /api/todos/{id} - Atualiza uma tarefa
 * DELETE /api/todos/{id} - Remove uma tarefa
 */
Route::apiResource('todos', TodoController::class);

/**
 * Rotas da API para gerenciar atividades (CRUD completo)
 * 
 * GET    /api/atividades      - Lista todas as atividades
 * POST   /api/atividades      - Cria uma nova atividade
 * GET    /api/atividades/{id} - Mostra uma atividade específica
 * PUT    /api/atividades/{id} - Atualiza uma atividade
 * DELETE /api/atividades/{id} - Remove uma atividade
 * GET    /api/atividades/status/{status} - Lista atividades por status
 * GET    /api/atividades/prioridade/{prioridade} - Lista atividades por prioridade
 * GET    /api/atividades/categoria/{categoria} - Lista atividades por categoria
 * GET    /api/atividades/favoritas - Lista atividades favoritas
 * GET    /api/atividades/vencidas - Lista atividades vencidas
 * GET    /api/atividades/lembretes - Lista atividades com lembrete
 * GET    /api/atividades/estatisticas - Estatísticas das atividades
 * PUT    /api/atividades/{id}/progresso - Atualizar progresso
 * POST   /api/atividades/{id}/tags - Adicionar tag
 * DELETE /api/atividades/{id}/tags - Remover tag
 */
Route::apiResource('atividades', AtividadeController::class);
Route::get('atividades/status/{status}', [AtividadeController::class, 'getByStatus']);
Route::get('atividades/prioridade/{prioridade}', [AtividadeController::class, 'getByPriority']);
Route::get('atividades/categoria/{categoria}', [AtividadeController::class, 'getByCategoria']);
Route::get('atividades/favoritas', [AtividadeController::class, 'getFavoritas']);
Route::get('atividades/vencidas', [AtividadeController::class, 'getVencidas']);
Route::get('atividades/lembretes', [AtividadeController::class, 'getComLembrete']);
Route::get('atividades/estatisticas', [AtividadeController::class, 'getEstatisticas']);
Route::get('atividades/ativas', [AtividadeController::class, 'getAtivas']);
Route::get('atividades/concluidas', [AtividadeController::class, 'getConcluidas']);
Route::put('atividades/{atividade}/progresso', [AtividadeController::class, 'atualizarProgresso']);
Route::post('atividades/{atividade}/tags', [AtividadeController::class, 'adicionarTag']);
Route::delete('atividades/{atividade}/tags', [AtividadeController::class, 'removerTag']); 
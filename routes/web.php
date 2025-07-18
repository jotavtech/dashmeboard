<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\AuthController;

/**
 * Rotas Web
 * 
 * Estas rotas servem a interface do usuário (frontend React)
 * e são acessadas diretamente pelo navegador
 */

/**
 * Rotas de Autenticação (Públicas)
 */
// Rota inicial - redireciona para login ou dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Rotas de Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

// Rotas de Registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

/**
 * Rotas Protegidas (Requer Autenticação)
 */
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Perfil do usuário
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Rota principal da aplicação - redirecionada para atividades
    Route::get('/todo', function () {
        return redirect('/atividades');
    });
    
    // Rotas de atividades
    Route::get('/atividades', function () {
        return view('atividades.index');
    })->name('atividades');
});

// Test routes (mantendo para desenvolvimento)
Route::get('/test', function () {
    return response()->json(['message' => 'Test route working!']);
});

Route::get('/api/test', function () {
    return response()->json(['message' => 'API test route working!']);
});

/**
 * Rotas de API (Protegidas)
 */
Route::middleware('auth')->prefix('api')->group(function () {
    // API de atividades - filtra por usuário logado
    Route::apiResource('atividades', AtividadeController::class);
    Route::get('atividades/status/{status}', [AtividadeController::class, 'getByStatus']);
    Route::get('atividades/prioridade/{prioridade}', [AtividadeController::class, 'getByPriority']);
    Route::get('atividades/categoria/{categoria}', [AtividadeController::class, 'getByCategoria']);
    Route::get('atividades/favoritas', [AtividadeController::class, 'getFavoritas']);
    Route::get('atividades/vencidas', [AtividadeController::class, 'getVencidas']);
    Route::get('atividades/lembretes', [AtividadeController::class, 'getComLembrete']);
    Route::get('atividades/estatisticas', [AtividadeController::class, 'getEstatisticas']);
    Route::put('atividades/{atividade}/progresso', [AtividadeController::class, 'atualizarProgresso']);
    Route::post('atividades/{atividade}/tags', [AtividadeController::class, 'adicionarTag']);
    Route::delete('atividades/{atividade}/tags', [AtividadeController::class, 'removerTag']);
});

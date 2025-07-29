<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Helpers\CalendarHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    
    // Perfil do usuário (removido - usando novo sistema de perfis)
    // Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    // Route::post('/profile', [AuthController::class, 'updateProfile']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Rota principal da aplicação - redirecionada para atividades
    Route::get('/todo', function () {
        return redirect('/atividades');
    });
    
    // Rota para visualizar histórico (atividades arquivadas) - MOVIDA PARA O INÍCIO
    Route::get('/atividades/historico', function () {
        try {
            // Temporary workaround: show concluded activities as "archived"
            $atividades = \App\Models\Atividade::where('user_id', auth()->id())
                ->where('status', 'concluida')
                ->orderBy('completed_at', 'desc')
                ->get();
            
            return view('atividades.historico', compact('atividades'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('atividades.historico');
    

    

    
    // Rotas de atividades
    Route::get('/atividades', function () {
        $atividades = \App\Models\Atividade::where('user_id', auth()->id())
            ->naoArquivadas()
            ->orderBy('created_at', 'desc')
            ->get();
        $categorias = \App\Models\Category::all();
        
        return view('atividades.index', compact('atividades', 'categorias'));
    })->name('atividades');
    
    // Rotas da Agenda
    Route::get('/agenda', function() {
        try {
            $user = auth()->user();
            $currentMonth = request('month', date('Y-m'));
            
            // Gerar calendário
            $calendar = CalendarHelper::generateCalendar($currentMonth, $user->id);
            
            try {
                $agendaItems = DB::table('agenda_items')
                    ->select('*', DB::raw('atividade_id'))
                    ->where('user_id', $user->id)
                    ->orderBy('date')
                    ->orderBy('time')
                    ->get();
            } catch (\Exception $e) {
                // Se a coluna não existe, usar consulta sem ela
                $agendaItems = DB::table('agenda_items')
                    ->where('user_id', $user->id)
                    ->orderBy('date')
                    ->orderBy('time')
                    ->get();
                
                // Adicionar propriedade atividade_id como null para todos os itens
                $agendaItems = $agendaItems->map(function($item) {
                    $item->atividade_id = null;
                    return $item;
                });
            }

            return view('agenda.index', compact('agendaItems', 'currentMonth', 'calendar'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.index');
    
    // Rota específica para criar atividade deve vir ANTES da rota genérica
    Route::post('/agenda/{id}/create-atividade', function($id) {
        try {
            $agendaItem = DB::table('agenda_items')->find($id);
            
            if (!$agendaItem || $agendaItem->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado'], 403);
            }

            // Verificar se já existe uma atividade para este item da agenda
            if (isset($agendaItem->atividade_id) && $agendaItem->atividade_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma atividade criada para este item da agenda'
                ]);
            }

            // Criar nova atividade usando transação para melhor performance
            DB::beginTransaction();
            
            try {
                $atividade = \App\Models\Atividade::create([
                    'user_id' => auth()->id(),
                    'titulo' => $agendaItem->title,
                    'descricao' => $agendaItem->description,
                    'status' => 'pendente',
                    'prioridade' => 'media',
                    'data_inicio' => $agendaItem->date,
                    'data_fim' => $agendaItem->date,
                    'categoria_id' => null
                ]);

                // Atualizar o agenda_item com o ID da atividade
                DB::table('agenda_items')->where('id', $id)->update([
                    'atividade_id' => $atividade->id
                ]);
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Atividade criada com sucesso!',
                    'atividade' => $atividade
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Erro ao criar atividade:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'agenda_item_id' => $id,
                    'user_id' => auth()->id()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Erro geral na criação de atividade:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.create-atividade');

    Route::get('/agenda/{id}', function($id) {
        try {
            $agendaItem = DB::table('agenda_items')->find($id);
            
            if (!$agendaItem || $agendaItem->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado'], 403);
            }

            // Garantir que a propriedade atividade_id existe
            if (!isset($agendaItem->atividade_id)) {
                $agendaItem->atividade_id = null;
            }

            return response()->json($agendaItem);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.show');
    

    
    Route::post('/agenda', function(Request $request) {
        try {
            // Verificar se o usuário está autenticado
            if (!auth()->check()) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'time' => 'nullable',
                'is_public' => 'nullable',
                'color' => 'nullable|string|max:7',
                'status' => 'nullable|in:pending,completed,cancelled'
            ]);

            // Simplificar a lógica do is_public
            $isPublic = in_array($request->input('is_public'), [true, 'true', 1, '1', 'on', 'checked', 'yes']);

            $agendaItemId = DB::table('agenda_items')->insertGetId([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time ?: null,
                'is_public' => $isPublic ? 1 : 0,
                'color' => $request->color ?? '#007bff',
                'status' => $request->status ?? 'pending',
                'atividade_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $agendaItem = DB::table('agenda_items')->find($agendaItemId);

            return response()->json([
                'success' => true,
                'message' => 'Atividade adicionada com sucesso!',
                'agendaItem' => $agendaItem
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar atividade:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.store');
    
    Route::put('/agenda/{id}', function(Request $request, $id) {
        try {
            $agendaItem = DB::table('agenda_items')->find($id);
            
            if (!$agendaItem || $agendaItem->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado'], 403);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'time' => 'nullable',
                'is_public' => 'nullable',
                'color' => 'nullable|string|max:7',
                'status' => 'nullable|in:pending,completed,cancelled'
            ]);

            // Simplificar a lógica do is_public
            $isPublic = in_array($request->input('is_public'), [true, 'true', 1, '1', 'on', 'checked', 'yes']);

            DB::table('agenda_items')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time ?: null,
                'is_public' => $isPublic ? 1 : 0,
                'color' => $request->color ?? '#007bff',
                'status' => $request->status ?? 'pending',
                'updated_at' => now()
            ]);

            $updatedItem = DB::table('agenda_items')->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Atividade atualizada com sucesso!',
                'agendaItem' => $updatedItem
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.update');
    
    Route::delete('/agenda/{id}', function($id) {
        try {
            $agendaItem = DB::table('agenda_items')->find($id);
            
            if (!$agendaItem || $agendaItem->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado'], 403);
            }

            DB::table('agenda_items')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Atividade removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.destroy');
    
    Route::get('/agenda/day/{date}', function($date) {
        try {
            $user = auth()->user();
            
            $activities = DB::table('agenda_items')
                ->where('user_id', $user->id)
                ->where('date', $date)
                ->orderBy('time')
                ->get();
            
            // Garantir que a propriedade atividade_id existe para todos os itens
            $activities = $activities->map(function($item) {
                if (!isset($item->atividade_id)) {
                    $item->atividade_id = null;
                }
                return $item;
            });
            
            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('agenda.day');
    
    // Rota de teste para verificar se o problema é específico
    Route::get('/agenda/test', function() {
        return response()->json(['message' => 'Rota de teste funcionando']);
    })->name('agenda.test');

    // Rota de teste para criação de atividade
    Route::post('/test-create-activity', function() {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Não autenticado'], 401);
            }
            
            $atividade = \App\Models\Atividade::create([
                'user_id' => auth()->id(),
                'titulo' => 'Teste de Atividade',
                'descricao' => 'Descrição de teste',
                'status' => 'pendente',
                'prioridade' => 'media',
                'categoria_id' => null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade de teste criada com sucesso!',
                'atividade' => $atividade
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro no teste de criação:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('test.create-activity');


    
    // Rotas de criação e edição de atividades
    Route::post('/save-activity', function () {
        try {
            // Verificar se o usuário está autenticado
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Usuário não autenticado'], 401);
            }
            
            $data = request()->all();
            
            // Validação básica
            if (empty($data['titulo'])) {
                return response()->json(['success' => false, 'message' => 'Título é obrigatório'], 400);
            }
            
            // Preparar dados para criação
            $atividadeData = [
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'] ?? '',
                'status' => $data['status'] ?? 'pendente',
                'prioridade' => $data['prioridade'] ?? 'media',
                'data_inicio' => $data['data_inicio'] ?? null,
                'data_fim' => $data['data_fim'] ?? null,
                'categoria_id' => $data['categoria_id'] ?? null,
                'user_id' => auth()->id()
            ];
            
            // Log para debug
            \Log::info('Criando atividade:', $atividadeData);
            
            // Criar atividade
            $atividade = \App\Models\Atividade::create($atividadeData);
            
            \Log::info('Atividade criada com sucesso:', ['id' => $atividade->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade criada com sucesso!',
                'atividade' => $atividade
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar atividade:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => request()->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar atividade: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.store');
    
    Route::put('/update-activity/{id}', function ($id) {
        try {
            $atividade = \App\Models\Atividade::where('user_id', auth()->id())->find($id);
            
            if (!$atividade) {
                return response()->json(['success' => false, 'message' => 'Atividade não encontrada'], 404);
            }
            
            $data = request()->all();
            
            // Validar dados
            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'titulo' => 'sometimes|required|string|max:255',
                'descricao' => 'nullable|string',
                'status' => 'sometimes|required|in:pendente,em_andamento,concluida',
                'prioridade' => 'sometimes|required|in:baixa,media,alta',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date',
                'progresso' => 'nullable|integer|min:0|max:100',
                'categoria_id' => 'nullable|integer|exists:categories,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Preparar dados para atualização
            $updateData = [];
            if (isset($data['titulo'])) $updateData['titulo'] = $data['titulo'];
            if (isset($data['descricao'])) $updateData['descricao'] = $data['descricao'];
            if (isset($data['status'])) $updateData['status'] = $data['status'];
            if (isset($data['prioridade'])) $updateData['prioridade'] = $data['prioridade'];
            if (isset($data['data_inicio'])) $updateData['data_inicio'] = $data['data_inicio'];
            if (isset($data['data_fim'])) $updateData['data_fim'] = $data['data_fim'];
            if (isset($data['progresso'])) $updateData['progresso'] = (int)$data['progresso'];
            if (isset($data['categoria_id'])) $updateData['categoria_id'] = $data['categoria_id'] ? (int)$data['categoria_id'] : null;
            
            $atividade->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade atualizada com sucesso!',
                'atividade' => $atividade
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar atividade: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.update');
    
    Route::delete('/delete-activity/{id}', function ($id) {
        try {
            $atividade = \App\Models\Atividade::where('user_id', auth()->id())->find($id);
            
            if (!$atividade) {
                return response()->json(['success' => false, 'message' => 'Atividade não encontrada'], 404);
            }
            
            $atividade->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade removida com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover atividade: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.destroy');
    
    // Rota para arquivar atividade (mover para histórico)
    Route::put('/archive-activity/{id}', function ($id) {
        try {
            $atividade = \App\Models\Atividade::where('user_id', auth()->id())->find($id);
            
            if (!$atividade) {
                return response()->json(['success' => false, 'message' => 'Atividade não encontrada'], 404);
            }
            
            // Marcar como arquivada e definir data de conclusão se não existir
            $updateData = [
                'status' => 'concluida',
                'archived' => true,
                'archived_at' => now()
            ];
            
            if (!$atividade->completed_at) {
                $updateData['completed_at'] = now();
            }
            
            $atividade->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade movida para o histórico com sucesso!',
                'atividade' => $atividade
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao arquivar atividade: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.archive');
    
    // Rota para restaurar atividade do histórico
    Route::put('/restore-activity/{id}', function ($id) {
        try {
            $atividade = \App\Models\Atividade::where('user_id', auth()->id())->find($id);
            
            if (!$atividade) {
                return response()->json(['success' => false, 'message' => 'Atividade não encontrada'], 404);
            }
            
            // Restaurar atividade (remover flag de arquivada)
            $atividade->update([
                'archived' => false,
                'archived_at' => null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Atividade restaurada com sucesso!',
                'atividade' => $atividade
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar atividade: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.restore');

    // Nova rota para atualizar status das atividades
    Route::put('/atividades/{id}/status', function ($id) {
        try {
            $atividade = \App\Models\Atividade::where('user_id', auth()->id())->find($id);
            
            if (!$atividade) {
                return response()->json(['success' => false, 'message' => 'Atividade não encontrada'], 404);
            }
            
            $status = request('status');
            if (!in_array($status, ['pendente', 'em_andamento', 'concluida'])) {
                return response()->json(['success' => false, 'message' => 'Status inválido'], 400);
            }
            
            $atividade->update(['status' => $status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!',
                'atividade' => $atividade
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    })->name('atividades.update-status');

    // Rotas de Perfis
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/search', [ProfileController::class, 'search'])->name('profiles.search');
    Route::get('/profiles/my-profile', [ProfileController::class, 'myProfile'])->name('profiles.my-profile');
    Route::get('/profiles/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
    Route::post('/profiles/update', [ProfileController::class, 'update'])->name('profiles.update');
    Route::get('/profiles/{username}', [ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/fortune-cookie', function () {
        return view('profiles.fortune-cookie', [
            'profile' => auth()->user()->profile,
            'fortuneMessages' => [
                'O código que você escreve hoje será a base do futuro de amanhã. Continue programando com paixão! 💻✨',
                'A criatividade é infinita. Deixe sua imaginação voar e transforme ideias em realidade! 🎨🚀',
                'O sucesso não é acidente. É resultado de trabalho duro, persistência e determinação. Continue firme! 💪🔥',
                'A mente é como um jardim. Plante pensamentos positivos e colha felicidade! 🌱🧠',
                'A música é a linguagem universal da alma. Deixe suas emoções fluírem através das notas! 🎼🎵',
                'A paz interior é o maior tesouro. Respire fundo e encontre sua serenidade! 🧘‍♀️🌿',
                'Os dados não mentem. Use-os com sabedoria para criar um futuro melhor! 📈🧠',
                'A arte é a expressão mais pura da alma. Deixe sua criatividade brilhar! ✨🎨',
                'Seu corpo pode fazer qualquer coisa. É sua mente que você precisa convencer! 💪🔥',
                'A autenticidade é sua maior força. Seja você mesmo e inspire outros! 🌟📸',
                'A tecnologia é a ponte entre o sonho e a realidade. Continue construindo! 🌉💡',
                'Cada linha de código é uma oportunidade de criar algo incrível. Não desista! 🚀💻',
                'A inovação nasce da curiosidade. Continue explorando e descobrindo! 🔍💭',
                'O conhecimento é o investimento que sempre retorna dividendos. Continue aprendendo! 📚🎓',
                'A colaboração multiplica o sucesso. Conecte-se e cresça junto! 🤝🌟'
            ],
            'backgroundImages' => [
                'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146615/assets_task_01k0qsyhx4fbjbq7k8z19bxkw2_1753145794_img_0_aza4ph.webp',
                'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146672/662902ceb3ffcae10a826e3250ff7c4e_ox9tyq.jpg'
            ]
        ]);
    })->name('fortune-cookie');
    
    Route::post('/fortune-cookie/update-background', function () {
        $user = auth()->user();
        $profile = $user->profile;
        
        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Perfil não encontrado'], 404);
        }

        request()->validate([
            'background_image_url' => 'required|url',
            'fortune_message' => 'nullable|string|max:500'
        ]);

        $profile->background_image_url = request('background_image_url');
        
        if (request('fortune_message')) {
            $profile->fortune_cookie_message = request('fortune_message');
        }

        $profile->save();

        return response()->json([
            'success' => true, 
            'message' => 'Imagem de fundo atualizada com sucesso!',
            'background_image_url' => $profile->background_image_url
        ]);
    })->name('update-background');

    // Nova rota para salvar escolha do plano de fundo
    Route::post('/profiles/update-background', function () {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Perfil não encontrado'], 404);
        }

        request()->validate([
            'background_image_url' => 'required|url'
        ]);

        $profile->background_image_url = request('background_image_url');
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Plano de fundo atualizado com sucesso!',
            'background_image_url' => $profile->background_image_url
        ]);
    })->name('profiles.update-background');

    // Rotas de Mensagens
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/conversations', [MessageController::class, 'conversations'])->name('messages.conversations');
    Route::get('/messages/create/{username?}', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/conversation/{username}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/messages/search-users', [MessageController::class, 'searchUsers'])->name('messages.search-users');

    // Rotas extras de navegação
    Route::view('/settings', 'settings')->name('settings');
    Route::view('/notifications', 'notifications')->name('notifications');
    Route::view('/help', 'help')->name('help');
    Route::view('/backup', 'backup')->name('backup');
    Route::view('/import', 'import')->name('import');
    Route::view('/export', 'export')->name('export');
});

// Test routes (mantendo para desenvolvimento)
Route::get('/test', function () {
    return response()->json(['message' => 'Test route working!']);
});

// Test route for atividades
Route::get('/test-atividades', function () {
    return response()->json(['message' => 'Atividades route test working!']);
})->name('test.atividades');



Route::post('/test-csrf', function () {
    return response()->json([
        'message' => 'CSRF test working!',
        'csrf_token' => csrf_token(),
        'data' => request()->all()
    ]);
});





Route::get('/api/test', function () {
    return response()->json(['message' => 'API test route working!']);
});



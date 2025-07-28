<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AtividadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,em_andamento,concluida',
            'prioridade' => 'required|in:baixa,media,alta',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'progresso' => 'nullable|integer|min:0|max:100',
            'categoria_id' => 'nullable|integer|exists:categories,id',
        ]);

        // Extrair apenas os campos que existem na tabela
        $atividadeData = [
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'prioridade' => $request->prioridade,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'progresso' => $request->progresso ? (int)$request->progresso : 0,
            'categoria_id' => $request->categoria_id ? (int)$request->categoria_id : null,
            'user_id' => Auth::id()
        ];

        $atividade = Atividade::create($atividadeData);

        return response()->json([
            'success' => true,
            'message' => 'Atividade criada com sucesso!',
            'atividade' => $atividade
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Atividade $atividade): JsonResponse
    {
        // Verificar se a atividade pertence ao usuário logado
        if ($atividade->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Atividade não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $atividade
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atividade $atividade): JsonResponse
    {
        // Verificar se a atividade pertence ao usuário logado
        if ($atividade->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Atividade não encontrada'
            ], 404);
        }

        $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'sometimes|required|in:pendente,em_andamento,concluida',
            'prioridade' => 'sometimes|required|in:baixa,media,alta',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'progresso' => 'nullable|integer|min:0|max:100',
            'categoria_id' => 'nullable|integer|exists:categories,id',
        ]);

        // Extrair apenas os campos que existem na tabela
        $updateData = [];
        if ($request->has('titulo')) $updateData['titulo'] = $request->titulo;
        if ($request->has('descricao')) $updateData['descricao'] = $request->descricao;
        if ($request->has('status')) $updateData['status'] = $request->status;
        if ($request->has('prioridade')) $updateData['prioridade'] = $request->prioridade;
        if ($request->has('data_inicio')) $updateData['data_inicio'] = $request->data_inicio;
        if ($request->has('data_fim')) $updateData['data_fim'] = $request->data_fim;
        if ($request->has('progresso')) $updateData['progresso'] = (int)$request->progresso;
        if ($request->has('categoria_id')) $updateData['categoria_id'] = $request->categoria_id ? (int)$request->categoria_id : null;

        $atividade->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Atividade atualizada com sucesso!',
            'atividade' => $atividade
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atividade $atividade): JsonResponse
    {
        // Verificar se a atividade pertence ao usuário logado
        if ($atividade->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Atividade não encontrada'
            ], 404);
        }

        $atividade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Atividade excluída com sucesso!'
        ]);
    }

    /**
     * Get atividades by status
     */
    public function getByStatus($status): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades by priority
     */
    public function getByPriority($priority): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->where('prioridade', $priority)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades favoritas
     */
    public function getFavoritas(): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->favoritas()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades por categoria
     */
    public function getByCategoria($categoria): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->categoria($categoria)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades vencidas
     */
    public function getVencidas(): JsonResponse
    {
        $atividades = Atividade::vencidas()
            ->orderBy('data_fim', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades com lembrete
     */
    public function getComLembrete(): JsonResponse
    {
        $atividades = Atividade::comLembrete()
            ->where('lembrete', '<=', now()->addDays(7))
            ->orderBy('lembrete', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Atualizar progresso de uma atividade
     */
    public function atualizarProgresso(Request $request, Atividade $atividade): JsonResponse
    {
        $request->validate([
            'progresso' => 'required|integer|between:0,100'
        ]);

        $atividade->atualizarProgresso($request->progresso);

        return response()->json([
            'success' => true,
            'message' => 'Progresso atualizado com sucesso!',
            'data' => $atividade
        ]);
    }

    /**
     * Adicionar tag a uma atividade
     */
    public function adicionarTag(Request $request, Atividade $atividade): JsonResponse
    {
        $request->validate([
            'tag' => 'required|string|max:50'
        ]);

        $atividade->adicionarTag($request->tag);

        return response()->json([
            'success' => true,
            'message' => 'Tag adicionada com sucesso!',
            'data' => $atividade
        ]);
    }

    /**
     * Remover tag de uma atividade
     */
    public function removerTag(Request $request, Atividade $atividade): JsonResponse
    {
        $request->validate([
            'tag' => 'required|string|max:50'
        ]);

        $atividade->removerTag($request->tag);

        return response()->json([
            'success' => true,
            'message' => 'Tag removida com sucesso!',
            'data' => $atividade
        ]);
    }

    /**
     * Atualizar status de uma atividade
     */
    public function updateStatus(Request $request, Atividade $atividade): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pendente,em_andamento,concluida'
        ]);

        // Verificar se a atividade pertence ao usuário logado
        if ($atividade->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Atividade não encontrada'
            ], 404);
        }

        $updateData = ['status' => $request->status];

        // Se a atividade foi concluída, adicionar timestamp de conclusão
        if ($request->status === 'concluida') {
            $updateData['completed_at'] = now();
            $updateData['progresso'] = 100; // Garantir que o progresso seja 100%
        } else {
            // Se não foi concluída, remover timestamp de conclusão
            $updateData['completed_at'] = null;
        }

        $atividade->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
            'data' => $atividade
        ]);
    }

    /**
     * Get atividades ativas (não concluídas)
     */
    public function getAtivas(): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->ativas()
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get atividades concluídas (histórico)
     */
    public function getConcluidas(): JsonResponse
    {
        $atividades = Atividade::where('user_id', Auth::id())
            ->concluidas()
            ->orderBy('completed_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $atividades
        ]);
    }

    /**
     * Get estatísticas das atividades
     */
    public function getEstatisticas(): JsonResponse
    {
        $total = Atividade::where('user_id', Auth::id())->count();
        $pendentes = Atividade::where('user_id', Auth::id())->pendentes()->count();
        $emAndamento = Atividade::where('user_id', Auth::id())->emAndamento()->count();
        $concluidas = Atividade::where('user_id', Auth::id())->concluidas()->count();
        $favoritas = Atividade::where('user_id', Auth::id())->favoritas()->count();
        $vencidas = Atividade::where('user_id', Auth::id())->vencidas()->count();

        $tempoTotalEstimado = Atividade::where('user_id', Auth::id())->sum('tempo_estimado');
        $tempoTotalReal = Atividade::where('user_id', Auth::id())->sum('tempo_real');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'pendentes' => $pendentes,
                'em_andamento' => $emAndamento,
                'concluidas' => $concluidas,
                'favoritas' => $favoritas,
                'vencidas' => $vencidas,
                'tempo_estimado_total' => $tempoTotalEstimado,
                'tempo_real_total' => $tempoTotalReal,
                'eficiencia_geral' => $tempoTotalEstimado && $tempoTotalReal ? 
                    round(($tempoTotalEstimado / $tempoTotalReal) * 100, 1) : null
            ]
        ]);
    }
} 
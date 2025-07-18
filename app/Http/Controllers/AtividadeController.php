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
            'data_limite' => 'nullable|date',
            'categoria' => 'nullable|string|max:255', // Para compatibilidade com import JSON
        ]);

        // Extrair apenas os campos que existem na tabela
        $atividadeData = [
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'prioridade' => $request->prioridade,
            'data_limite' => $request->data_limite,
            'user_id' => Auth::id()
        ];

        $atividade = Atividade::create($atividadeData);

        return response()->json([
            'success' => true,
            'message' => 'Atividade criada com sucesso!',
            'data' => $atividade
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
            'data_limite' => 'nullable|date',
        ]);

        // Extrair apenas os campos que existem na tabela
        $updateData = [];
        if ($request->has('titulo')) $updateData['titulo'] = $request->titulo;
        if ($request->has('descricao')) $updateData['descricao'] = $request->descricao;
        if ($request->has('status')) $updateData['status'] = $request->status;
        if ($request->has('prioridade')) $updateData['prioridade'] = $request->prioridade;
        if ($request->has('data_limite')) $updateData['data_limite'] = $request->data_limite;

        $atividade->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Atividade atualizada com sucesso!',
            'data' => $atividade
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
     * Get estatísticas das atividades
     */
    public function getEstatisticas(): JsonResponse
    {
        $total = Atividade::count();
        $pendentes = Atividade::pendentes()->count();
        $emAndamento = Atividade::emAndamento()->count();
        $concluidas = Atividade::concluidas()->count();
        $favoritas = Atividade::favoritas()->count();
        $vencidas = Atividade::vencidas()->count();

        $tempoTotalEstimado = Atividade::sum('tempo_estimado');
        $tempoTotalReal = Atividade::sum('tempo_real');

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
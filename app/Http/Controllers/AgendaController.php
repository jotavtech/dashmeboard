<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = request('month', date('Y-m'));
        
        // Buscar todas as atividades do usuário
        $agendaItems = DB::table('agenda_items')
            ->where('user_id', $user->id)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $calendar = $this->generateCalendar($currentMonth, $agendaItems);

        return view('agenda.index', compact('calendar', 'agendaItems', 'currentMonth'));
    }

    public function show($id)
    {
        $agendaItem = DB::table('agenda_items')->find($id);
        
        if (!$agendaItem || $agendaItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        return response()->json($agendaItem);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'is_public' => 'boolean',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:pending,completed,cancelled',
            'create_atividade' => 'boolean'
        ]);

        $agendaItemId = DB::table('agenda_items')->insertGetId([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'is_public' => $request->boolean('is_public'),
            'color' => $request->color ?? '#007bff',
            'status' => $request->status ?? 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $agendaItem = DB::table('agenda_items')->find($agendaItemId);

        // Se solicitado, criar também uma atividade
        $atividade = null;
        if ($request->boolean('create_atividade')) {
            $atividade = Atividade::create([
                'user_id' => Auth::id(),
                'titulo' => $request->title,
                'descricao' => $request->description,
                'status' => 'pendente',
                'prioridade' => 'media',
                'data_inicio' => $request->date,
                'data_fim' => $request->date,
                'progresso' => 0,
                'categoria_id' => null
            ]);

            // Atualizar o agenda_item com o ID da atividade
            DB::table('agenda_items')->where('id', $agendaItemId)->update([
                'atividade_id' => $atividade->id
            ]);

            $agendaItem = DB::table('agenda_items')->find($agendaItemId);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atividade adicionada com sucesso!',
                'agendaItem' => $agendaItem,
                'atividade' => $atividade
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Atividade adicionada com sucesso!');
    }

    public function createAtividadeFromAgenda($id)
    {
        $agendaItem = DB::table('agenda_items')->find($id);
        
        if (!$agendaItem || $agendaItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Verificar se já existe uma atividade para este item da agenda
        if ($agendaItem->atividade_id) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma atividade criada para este item da agenda'
            ]);
        }

        // Criar nova atividade
        $atividade = Atividade::create([
            'user_id' => Auth::id(),
            'titulo' => $agendaItem->title,
            'descricao' => $agendaItem->description,
            'status' => 'pendente',
            'prioridade' => 'media',
            'data_inicio' => $agendaItem->date,
            'data_fim' => $agendaItem->date,
            'progresso' => 0,
            'categoria_id' => null
        ]);

        // Atualizar o agenda_item com o ID da atividade
        DB::table('agenda_items')->where('id', $id)->update([
            'atividade_id' => $atividade->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Atividade criada com sucesso!',
            'atividade' => $atividade
        ]);
    }

    public function update(Request $request, $id)
    {
        $agendaItem = DB::table('agenda_items')->find($id);
        
        if (!$agendaItem || $agendaItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'is_public' => 'boolean',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:pending,completed,cancelled'
        ]);

        DB::table('agenda_items')->where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'is_public' => $request->boolean('is_public'),
            'color' => $request->color ?? '#007bff',
            'status' => $request->status ?? 'pending',
            'updated_at' => now()
        ]);

        $updatedItem = DB::table('agenda_items')->find($id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atividade atualizada com sucesso!',
                'agendaItem' => $updatedItem
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Atividade atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $agendaItem = DB::table('agenda_items')->find($id);
        
        if (!$agendaItem || $agendaItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        DB::table('agenda_items')->where('id', $id)->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atividade removida com sucesso!'
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Atividade removida com sucesso!');
    }

    public function getDayActivities($date)
    {
        $activities = DB::table('agenda_items')
            ->where('user_id', Auth::id())
            ->where('date', $date)
            ->orderBy('time')
            ->get();

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    private function generateCalendar($month, $agendaItems)
    {
        $date = strtotime($month . '-01');
        $startOfMonth = strtotime('first day of this month', $date);
        $endOfMonth = strtotime('last day of this month', $date);
        $startOfWeek = strtotime('last sunday', $startOfMonth);
        $endOfWeek = strtotime('next saturday', $endOfMonth);

        $calendar = [];
        $currentDate = $startOfWeek;

        while ($currentDate <= $endOfWeek) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dayItems = $agendaItems->filter(function($item) use ($currentDate) {
                    return $item->date === date('Y-m-d', $currentDate);
                });
                
                $week[] = [
                    'date' => date('Y-m-d', $currentDate),
                    'day' => date('j', $currentDate),
                    'isCurrentMonth' => date('m', $currentDate) === date('m', $date),
                    'isToday' => date('Y-m-d', $currentDate) === date('Y-m-d'),
                    'activities' => $dayItems,
                    'activityCount' => $dayItems->count()
                ];
                
                $currentDate = strtotime('+1 day', $currentDate);
            }
            $calendar[] = $week;
        }

        return $calendar;
    }
} 
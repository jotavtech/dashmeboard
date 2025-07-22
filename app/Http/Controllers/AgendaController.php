<?php

namespace App\Http\Controllers;

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
            'status' => 'nullable|in:pending,completed,cancelled'
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Atividade adicionada com sucesso!',
                'agendaItem' => $agendaItem
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Atividade adicionada com sucesso!');
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
                    'date' => $currentDate,
                    'isCurrentMonth' => date('m', $currentDate) === date('m', $date),
                    'isToday' => date('Y-m-d', $currentDate) === date('Y-m-d'),
                    'items' => $dayItems,
                    'itemCount' => $dayItems->count()
                ];
                
                $currentDate = strtotime('+1 day', $currentDate);
            }
            $calendar[] = $week;
        }

        return $calendar;
    }
} 
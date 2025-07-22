<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarHelper
{
    /**
     * Gera o calendário para um mês específico
     *
     * @param string $month Formato Y-m (ex: 2025-01)
     * @param int $userId ID do usuário
     * @return array
     */
    public static function generateCalendar($month, $userId)
    {
        $date = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Buscar atividades do mês
        $activities = DB::table('agenda_items')
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            ->groupBy('date');
        
        // Gerar grid do calendário
        $calendar = [];
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayActivities = $activities->get($dateKey, collect());
            
            $calendar[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->day,
                'isCurrentMonth' => $currentDate->month === $date->month,
                'isToday' => $currentDate->isToday(),
                'activities' => $dayActivities,
                'activityCount' => $dayActivities->count()
            ];
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }
} 
<?php

namespace App\Policies;

use App\Models\AgendaItem;
use App\Models\User;

class AgendaItemPolicy
{
    public function view(User $user, AgendaItem $agendaItem): bool
    {
        return $user->id === $agendaItem->user_id;
    }

    public function update(User $user, AgendaItem $agendaItem): bool
    {
        return $user->id === $agendaItem->user_id;
    }

    public function delete(User $user, AgendaItem $agendaItem): bool
    {
        return $user->id === $agendaItem->user_id;
    }
} 
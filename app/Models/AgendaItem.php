<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaItem extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'time',
        'is_public',
        'color',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('H:i') : null;
    }

    public function getStatusColorAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'warning';
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'primary';
        }
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $table = 'atividades';

    protected $fillable = [
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'data_limite',
        'user_id'
    ];

    protected $casts = [
        'data_limite' => 'date',
    ];

    // Relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes para filtrar por status
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    // Scope para filtrar por prioridade
    public function scopePrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    // Acessor para texto da prioridade
    public function getPrioridadeTextoAttribute()
    {
        switch($this->prioridade) {
            case 'baixa':
                return 'Baixa';
            case 'media':
                return 'Média';
            case 'alta':
                return 'Alta';
            default:
                return 'Desconhecida';
        }
    }

    // Acessor para texto do status
    public function getStatusTextoAttribute()
    {
        switch($this->status) {
            case 'pendente':
                return 'Pendente';
            case 'em_andamento':
                return 'Em Andamento';
            case 'concluida':
                return 'Concluída';
            default:
                return 'Desconhecido';
        }
    }

    // Scope para atividades vencidas
    public function scopeVencidas($query)
    {
        return $query->where('data_limite', '<', now())
                    ->where('status', '!=', 'concluida');
    }
} 
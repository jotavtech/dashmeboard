<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $table = 'atividades';

    protected $fillable = [
        'user_id',
        'titulo',
        'categoria_id',
        'descricao',
        'status',
        'data_inicio',
        'data_fim',
        'prioridade',
        'progresso',
        'completed_at',
        'archived',
        'archived_at',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
        'archived' => 'boolean',
        'progresso' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'projeto_id');
    }

    public function agendaItems()
    {
        return $this->hasMany(AgendaItem::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'atividade_tag');
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
        return $query->where('status', 'concluida')->whereNotNull('completed_at');
    }

    public function scopeAtivas($query)
    {
        return $query->where('status', '!=', 'concluida')->orWhereNull('completed_at');
    }

    public function scopeArquivadas($query)
    {
        // Temporary workaround: return empty since archived column doesn't exist
        return $query->whereRaw('1 = 0');
    }

    public function scopeNaoArquivadas($query)
    {
        // Temporary workaround: return all activities since archived column doesn't exist
        return $query;
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
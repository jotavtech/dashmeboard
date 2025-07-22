<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cor',
    ];

    public function atividades()
    {
        return $this->belongsToMany(Atividade::class, 'atividade_tag');
    }
} 
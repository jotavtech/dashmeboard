<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cor',
    ];

    public function atividades()
    {
        return $this->hasMany(Atividade::class, 'categoria_id');
    }
} 
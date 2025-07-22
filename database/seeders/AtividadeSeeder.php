<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atividade;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

class AtividadeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $cat = Category::first();
        $proj = Project::first();
        $tag1 = Tag::where('nome', 'Urgente')->first();
        $tag2 = Tag::where('nome', 'Importante')->first();

        $atividade = Atividade::create([
            'user_id' => $user ? $user->id : 1,
            'titulo' => 'Exemplo de Atividade',
            'descricao' => 'Esta é uma atividade de exemplo com relacionamentos.',
            'status' => 'pendente',
            'data_inicio' => now()->subDays(2),
            'data_fim' => now()->addDays(5),
            'prioridade' => 'alta',
            'progresso' => 20,
            'categoria_id' => $cat ? $cat->id : null,
            'projeto_id' => $proj ? $proj->id : null,
            'favorita' => true,
            'lembrete' => now()->addDays(1),
        ]);

        if ($tag1 && $tag2) {
            $atividade->tags()->attach([$tag1->id, $tag2->id]);
        }
    }
} 
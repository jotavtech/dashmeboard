<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projetos = [
            [
                'nome' => 'DashMEBoard',
                'descricao' => 'Sistema de produtividade pessoal',
                'data_inicio' => now()->subDays(30),
                'data_fim' => now()->addDays(60),
                'status' => 'em_andamento',
            ],
            [
                'nome' => 'Portfólio Pessoal',
                'descricao' => 'Site para exibir projetos e habilidades',
                'data_inicio' => now()->subDays(10),
                'data_fim' => now()->addDays(20),
                'status' => 'planejado',
            ],
        ];
        foreach ($projetos as $proj) {
            Project::create($proj);
        }
    }
} 
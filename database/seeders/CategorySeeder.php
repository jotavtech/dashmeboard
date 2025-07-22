<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Trabalho', 'cor' => '#6366f1'],
            ['nome' => 'Estudos', 'cor' => '#10b981'],
            ['nome' => 'Pessoal', 'cor' => '#f59e0b'],
            ['nome' => 'Saúde', 'cor' => '#ef4444'],
            ['nome' => 'Lazer', 'cor' => '#8b5cf6'],
        ];
        foreach ($categorias as $cat) {
            Category::create($cat);
        }
    }
} 
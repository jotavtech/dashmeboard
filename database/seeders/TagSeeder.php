<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['nome' => 'Urgente', 'cor' => '#ef4444'],
            ['nome' => 'Importante', 'cor' => '#f59e0b'],
            ['nome' => 'Faculdade', 'cor' => '#6366f1'],
            ['nome' => 'Casa', 'cor' => '#10b981'],
            ['nome' => 'Trabalho', 'cor' => '#8b5cf6'],
        ];
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
} 
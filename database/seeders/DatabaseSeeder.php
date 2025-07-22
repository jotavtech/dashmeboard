<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
{
    \App\Models\User::factory()->create([
        'id' => 1,
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);
        $this->call([
            CategorySeeder::class,
            ProjectSeeder::class,
            TagSeeder::class,
            AtividadeSeeder::class,
        ]);
    }
} 
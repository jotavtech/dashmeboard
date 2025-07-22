<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Verificar se já existe um perfil para este usuário
            $existingProfile = Profile::where('user_id', $user->id)->first();
            
            if (!$existingProfile) {
                // Criar username baseado no nome do usuário
                $username = $this->generateUsername($user->name);
                $nickname = $this->generateNickname($user->name);

                Profile::create([
                    'user_id' => $user->id,
                    'username' => $username,
                    'nickname' => $nickname,
                    'bio' => 'Olá! Sou ' . $user->name . ' e estou usando o DashMEBoard para organizar minhas atividades.',
                    'profession' => $this->getRandomProfession(),
                    'mood' => $this->getRandomMood(),
                    'public_agenda' => 'Focando em produtividade e organização pessoal.',
                    'private_agenda' => 'Agenda privada do usuário.',
                    'daily_music' => $this->getRandomMusic(),
                    'fortune_cookie_message' => $this->getRandomFortune(),
                    'is_public' => true, // Todos os perfis serão públicos por padrão
                ]);
                
                echo "Perfil criado para: {$user->name} (@{$username} - {$nickname})\n";
            } else {
                echo "Perfil já existe para: {$user->name}\n";
            }
        }
    }
    
    private function generateUsername($name)
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $username = $base;
        $counter = 1;
        
        while (Profile::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }

    private function generateNickname($name)
    {
        $nicknames = [
            'Criativo', 'Explorador', 'Sonhador', 'Aventureiro', 'Sábio', 'Guerreiro',
            'Mestre', 'Aprendiz', 'Viajante', 'Artista', 'Pensador', 'Líder',
            'Inovador', 'Protetor', 'Mensageiro', 'Guardião', 'Visionário', 'Mestre',
            'Estrategista', 'Inspirador', 'Construtor', 'Desbravador', 'Harmonioso',
            'Determinado', 'Curioso', 'Paciente', 'Energético', 'Tranquilo', 'Focado'
        ];
        
        return $nicknames[array_rand($nicknames)];
    }
    
    private function getRandomProfession()
    {
        $professions = [
            'Desenvolvedor',
            'Designer',
            'Estudante',
            'Professor',
            'Analista',
            'Gerente',
            'Empreendedor',
            'Freelancer',
            'Pesquisador',
            'Consultor'
        ];
        
        return $professions[array_rand($professions)];
    }
    
    private function getRandomMood()
    {
        $moods = [
            '😊 Feliz',
            '🎯 Focado',
            '💪 Motivado',
            '🧘 Zen',
            '🚀 Produtivo',
            '🌟 Inspirado',
            '📚 Estudando',
            '💡 Criativo',
            '🎵 Relaxado',
            '🔥 Energético'
        ];
        
        return $moods[array_rand($moods)];
    }
    
    private function getRandomMusic()
    {
        $music = [
            'Lo-fi Hip Hop',
            'Classical',
            'Jazz',
            'Rock',
            'Pop',
            'Electronic',
            'Ambient',
            'Nature Sounds',
            'Podcast',
            'Silence'
        ];
        
        return $music[array_rand($music)];
    }
    
    private function getRandomFortune()
    {
        $fortunes = [
            'A produtividade é a chave do sucesso!',
            'Cada pequeno passo conta para grandes conquistas.',
            'Organize hoje, conquiste amanhã.',
            'A consistência supera a perfeição.',
            'Seu futuro é criado pelo que você faz hoje.',
            'Foque no progresso, não na perfeição.',
            'Cada tarefa concluída é uma vitória.',
            'A organização liberta a mente.',
            'Pequenas ações, grandes resultados.',
            'Você é mais forte do que pensa!'
        ];
        
        return $fortunes[array_rand($fortunes)];
    }
} 
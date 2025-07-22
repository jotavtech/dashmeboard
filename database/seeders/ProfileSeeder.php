<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuários e perfis de exemplo
        $profiles = [
            [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'username' => 'joaosilva',
                'mood' => '😊 Feliz e motivado',
                'public_agenda' => 'Desenvolvimento web, estudos de IA, projetos open source',
                'private_agenda' => 'Reunião com cliente às 14h, revisão de código às 16h',
                'daily_music' => 'Lofi Hip Hop - Coding Music',
                'profession' => 'Desenvolvedor Full Stack',
                'bio' => 'Apaixonado por tecnologia e inovação. Sempre buscando aprender novas tecnologias e criar soluções que fazem a diferença. Amante de café ☕ e música eletrônica.',
                'fortune_cookie_message' => 'O código que você escreve hoje será a base do futuro de amanhã. Continue programando com paixão! 💻✨',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@teste.com',
                'username' => 'mariasantos',
                'mood' => '🎨 Criativa e inspirada',
                'public_agenda' => 'Design de interfaces, workshops de UX, networking',
                'private_agenda' => 'Entrevista de emprego às 10h, reunião de equipe às 15h',
                'daily_music' => 'Indie Folk - Acoustic Vibes',
                'profession' => 'UX/UI Designer',
                'bio' => 'Designer apaixonada por criar experiências únicas e memoráveis. Acredito que o design pode mudar o mundo, uma interface de cada vez. 🌟',
                'fortune_cookie_message' => 'A criatividade é infinita. Deixe sua imaginação voar e transforme ideias em realidade! 🎨🚀',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Pedro Costa',
                'email' => 'pedro@teste.com',
                'username' => 'pedrocosta',
                'mood' => '💪 Focado e determinado',
                'public_agenda' => 'Treinos de musculação, estudos de marketing digital',
                'private_agenda' => 'Consulta médica às 9h, reunião com investidor às 17h',
                'daily_music' => 'Rock Clássico - Queen Greatest Hits',
                'profession' => 'Empreendedor Digital',
                'bio' => 'Empreendedor apaixonado por inovação e resultados. Transformando ideias em negócios de sucesso. Sempre em busca do próximo desafio! 🚀',
                'fortune_cookie_message' => 'O sucesso não é acidente. É resultado de trabalho duro, persistência e determinação. Continue firme! 💪🔥',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana@teste.com',
                'username' => 'anaoliveira',
                'mood' => '📚 Estudiosa e curiosa',
                'public_agenda' => 'Estudos de psicologia, leitura, pesquisa acadêmica',
                'private_agenda' => 'Aula de yoga às 7h, sessão de terapia às 18h',
                'daily_music' => 'Jazz Relaxante - Smooth Jazz Collection',
                'profession' => 'Psicóloga e Pesquisadora',
                'bio' => 'Psicóloga dedicada a entender a mente humana e ajudar pessoas a encontrarem seu caminho. Amante de livros e café. 📖☕',
                'fortune_cookie_message' => 'A mente é como um jardim. Plante pensamentos positivos e colha felicidade! 🌱🧠',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Carlos Ferreira',
                'email' => 'carlos@teste.com',
                'username' => 'carlosferreira',
                'mood' => '🎵 Musical e energético',
                'public_agenda' => 'Ensaio da banda, composição de músicas, shows',
                'private_agenda' => 'Gravação no estúdio às 13h, reunião com produtor às 20h',
                'daily_music' => 'Rock Alternativo - Arctic Monkeys',
                'profession' => 'Músico e Compositor',
                'bio' => 'Músico apaixonado por criar melodias que tocam a alma. Guitarrista e vocalista da banda "Ecos Urbanos". A música é minha vida! 🎸🎤',
                'fortune_cookie_message' => 'A música é a linguagem universal da alma. Deixe suas emoções fluírem através das notas! 🎼🎵',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Lucia Mendes',
                'email' => 'lucia@teste.com',
                'username' => 'luciamendes',
                'mood' => '🌱 Zen e equilibrada',
                'public_agenda' => 'Meditação, yoga, jardinagem, culinária vegana',
                'private_agenda' => 'Aula de meditação às 6h, preparação de refeições às 12h',
                'daily_music' => 'Nature Sounds - Forest Ambience',
                'profession' => 'Instrutora de Yoga e Coach de Vida',
                'bio' => 'Instrutora de yoga e coach de vida, ajudando pessoas a encontrarem equilíbrio e paz interior. Acredito no poder da transformação pessoal. 🧘‍♀️✨',
                'fortune_cookie_message' => 'A paz interior é o maior tesouro. Respire fundo e encontre sua serenidade! 🧘‍♀️🌿',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Roberto Almeida',
                'email' => 'roberto@teste.com',
                'username' => 'robertoalmeida',
                'mood' => '🔬 Analítico e metódico',
                'public_agenda' => 'Pesquisa científica, análise de dados, publicações',
                'private_agenda' => 'Reunião de laboratório às 8h, apresentação de pesquisa às 16h',
                'daily_music' => 'Classical Music - Mozart Symphony',
                'profession' => 'Cientista de Dados',
                'bio' => 'Cientista de dados apaixonado por desvendar padrões e extrair insights valiosos. Transformando dados em conhecimento que impulsiona inovação. 📊🔬',
                'fortune_cookie_message' => 'Os dados não mentem. Use-os com sabedoria para criar um futuro melhor! 📈🧠',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Fernanda Lima',
                'email' => 'fernanda@teste.com',
                'username' => 'fernandalima',
                'mood' => '🎭 Artística e expressiva',
                'public_agenda' => 'Ensaio de teatro, aulas de dança, workshops de arte',
                'private_agenda' => 'Audição para peça às 11h, ensaio de dança às 19h',
                'daily_music' => 'Bossa Nova - Tom Jobim',
                'profession' => 'Atriz e Dançarina',
                'bio' => 'Atriz e dançarina apaixonada por contar histórias através da arte. Acredito que a arte tem o poder de transformar vidas e conectar pessoas. 🎭💃',
                'fortune_cookie_message' => 'A arte é a expressão mais pura da alma. Deixe sua criatividade brilhar! ✨🎨',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Diego Santos',
                'email' => 'diego@teste.com',
                'username' => 'diegosantos',
                'mood' => '🏃‍♂️ Ativo e motivado',
                'public_agenda' => 'Corrida matinal, treinos de crossfit, preparação física',
                'private_agenda' => 'Corrida às 6h, treino de força às 18h',
                'daily_music' => 'Electronic - EDM Workout Mix',
                'profession' => 'Personal Trainer e Nutricionista',
                'bio' => 'Personal trainer e nutricionista dedicado a transformar vidas através do fitness e da alimentação saudável. Acredito que saúde é riqueza! 💪🥗',
                'fortune_cookie_message' => 'Seu corpo pode fazer qualquer coisa. É sua mente que você precisa convencer! 💪🔥',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
            [
                'name' => 'Samuel Rodrigues',
                'email' => 'samuel@teste.com',
                'username' => 'samuelrodrigues',
                'mood' => '📱 Conectado e social',
                'public_agenda' => 'Gestão de redes sociais, criação de conteúdo, networking',
                'private_agenda' => 'Reunião com cliente às 10h, live no Instagram às 20h',
                'daily_music' => 'Pop Hits - Top 40 Charts',
                'profession' => 'Influenciador Digital e Marketer',
                'bio' => 'Influenciador digital e marketer apaixonado por conectar marcas e pessoas. Criando conteúdo autêntico que inspira e engaja. 📱💫',
                'fortune_cookie_message' => 'A autenticidade é sua maior força. Seja você mesmo e inspire outros! 🌟📸',
                'is_public' => true,
                'profile_image' => null,
                'cover_image' => null,
            ],
        ];

        foreach ($profiles as $profileData) {
            // Verificar se usuário já existe
            $user = User::where('email', $profileData['email'])->first();
            
            if (!$user) {
                // Criar usuário
                $user = User::create([
                    'name' => $profileData['name'],
                    'email' => $profileData['email'],
                    'password' => Hash::make('123456'),
                ]);
            }

            // Verificar se perfil já existe
            $profile = Profile::where('user_id', $user->id)->first();
            
            if (!$profile) {
                // Criar perfil
                Profile::create([
                    'user_id' => $user->id,
                    'username' => $profileData['username'],
                    'mood' => $profileData['mood'],
                    'public_agenda' => $profileData['public_agenda'],
                    'private_agenda' => $profileData['private_agenda'],
                    'daily_music' => $profileData['daily_music'],
                    'profession' => $profileData['profession'],
                    'bio' => $profileData['bio'],
                    'fortune_cookie_message' => $profileData['fortune_cookie_message'],
                    'is_public' => $profileData['is_public'],
                    'profile_image' => $profileData['profile_image'],
                    'background_image' => $profileData['cover_image'],
                ]);
            }
        }

        echo "✅ 10 perfis de exemplo criados com sucesso!\n";
    }
} 
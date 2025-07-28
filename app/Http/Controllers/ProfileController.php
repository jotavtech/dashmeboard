<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $profiles = Profile::where('is_public', true)
            ->where('user_id', '!=', $user->id) // Excluir o próprio perfil do usuário
            ->with('user')
            ->paginate(12);
        
        return view('profiles.index', compact('profiles'));
    }

    public function show($username)
    {
        $profile = Profile::where('username', $username)
            ->where('is_public', true)
            ->with('user')
            ->firstOrFail();
        
        // Buscar atividades públicas da agenda
        $publicAgendaItems = DB::table('agenda_items')
            ->where('user_id', $profile->user_id)
            ->where('is_public', true)
            ->where('date', '>=', date('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();
        
        return view('profiles.show', compact('profile', 'publicAgendaItems'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            $profile = new Profile();
        }
        
        return view('profiles.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'username' => 'required|string|max:255|unique:profiles,username,' . ($user->profile ? $user->profile->id : ''),
            'bio' => 'nullable|string|max:1000',
            'profession' => 'nullable|string|max:255',
            'mood' => 'nullable|string|max:255',
            'public_agenda' => 'nullable|string|max:1000',
            'private_agenda' => 'nullable|string|max:1000',
            'daily_music' => 'nullable|string|max:255',
            'fortune_cookie_message' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean'
        ]);

        $profile = $user->profile;
        
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $profile->username = $request->username;
        $profile->bio = $request->bio;
        $profile->profession = $request->profession;
        $profile->mood = $request->mood;
        $profile->public_agenda = $request->public_agenda;
        $profile->private_agenda = $request->private_agenda;
        $profile->daily_music = $request->daily_music;
        $profile->fortune_cookie_message = $request->fortune_cookie_message;
        $profile->is_public = $request->has('is_public');

        // Upload de imagens
        try {
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $filename = 'profile_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Upload para Cloudinary
                $cloudinary = \Cloudinary\Uploader::upload($image->getRealPath(), [
                    'public_id' => $filename,
                    'folder' => 'profiles'
                ]);
                
                $profile->profile_image = $cloudinary['public_id'];
            }

            if ($request->hasFile('background_image')) {
                $image = $request->file('background_image');
                $filename = 'background_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Upload para Cloudinary
                $cloudinary = \Cloudinary\Uploader::upload($image->getRealPath(), [
                    'public_id' => $filename,
                    'folder' => 'backgrounds'
                ]);
                
                $profile->background_image = $cloudinary['public_id'];
            }
        } catch (\Exception $e) {
            // Log do erro mas não interromper o processo
            \Log::error('Erro no upload de imagem: ' . $e->getMessage());
        }

        $profile->save();

        return redirect()->route('profiles.show', $profile->username)
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $profession = $request->get('profession');
        $user = Auth::user();
        
        $profiles = Profile::where('is_public', true)
            ->where('user_id', '!=', $user->id) // Excluir o próprio perfil do usuário
            ->where(function($q) use ($query) {
                if ($query) {
                    $q->where('username', 'like', "%{$query}%")
                      ->orWhere('bio', 'like', "%{$query}%")
                      ->orWhere('profession', 'like', "%{$query}%")
                      ->orWhereHas('user', function($userQuery) use ($query) {
                          $userQuery->where('name', 'like', "%{$query}%");
                      });
                }
            })
            ->when($profession, function($q) use ($profession) {
                return $q->where('profession', $profession);
            })
            ->with('user')
            ->paginate(12);
        
        return view('profiles.search', compact('profiles', 'query', 'profession'));
    }

    public function myProfile()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            return redirect()->route('profiles.edit')
                ->with('info', 'Crie seu perfil primeiro!');
        }

        // Estatísticas do perfil antigo
        $atividades = $user->atividades()->where('created_at', '>=', now()->subDays(30))->get();
        $atividadesCount = $atividades->count();
        $atividadesConcluidas = $atividades->where('status', 'concluida')->count();
        $atividadesNoPrazo = $atividades->where('status', 'concluida')->filter(function($atividade) {
            return !$atividade->data_limite || $atividade->data_limite >= $atividade->updated_at;
        })->count();

        // Estatísticas por prioridade
        $prioridadeAlta = $atividades->where('prioridade', 'alta')->count();
        $prioridadeMedia = $atividades->where('prioridade', 'media')->count();
        $prioridadeBaixa = $atividades->where('prioridade', 'baixa')->count();

        // Estatísticas por status
        $statusConcluida = $atividades->where('status', 'concluida')->count();
        $statusEmAndamento = $atividades->where('status', 'em_andamento')->count();
        $statusPendente = $atividades->where('status', 'pendente')->count();

        // Atividades por dia (últimos 7 dias)
        $atividadesPorDia = $atividades->groupBy(function($atividade) {
            return $atividade->created_at->format('Y-m-d');
        })->map(function($group) {
            return ['data' => $group->first()->created_at->format('d/m'), 'count' => $group->count()];
        })->values();
        
        return view('profiles.my-profile', compact(
            'profile', 
            'atividadesCount', 
            'atividadesConcluidas', 
            'atividadesNoPrazo',
            'prioridadeAlta',
            'prioridadeMedia',
            'prioridadeBaixa',
            'statusConcluida',
            'statusEmAndamento',
            'statusPendente',
            'atividadesPorDia'
        ));
    }

    public function fortuneCookie()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            return redirect()->route('profiles.edit')
                ->with('info', 'Crie seu perfil primeiro!');
        }

        // Mensagens de biscoito da sorte
        $fortuneMessages = [
            'O código que você escreve hoje será a base do futuro de amanhã. Continue programando com paixão! 💻✨',
            'A criatividade é infinita. Deixe sua imaginação voar e transforme ideias em realidade! 🎨🚀',
            'O sucesso não é acidente. É resultado de trabalho duro, persistência e determinação. Continue firme! 💪🔥',
            'A mente é como um jardim. Plante pensamentos positivos e colha felicidade! 🌱🧠',
            'A música é a linguagem universal da alma. Deixe suas emoções fluírem através das notas! 🎼🎵',
            'A paz interior é o maior tesouro. Respire fundo e encontre sua serenidade! 🧘‍♀️🌿',
            'Os dados não mentem. Use-os com sabedoria para criar um futuro melhor! 📈🧠',
            'A arte é a expressão mais pura da alma. Deixe sua criatividade brilhar! ✨🎨',
            'Seu corpo pode fazer qualquer coisa. É sua mente que você precisa convencer! 💪🔥',
            'A autenticidade é sua maior força. Seja você mesmo e inspire outros! 🌟📸',
            'A tecnologia é a ponte entre o sonho e a realidade. Continue construindo! 🌉💡',
            'Cada linha de código é uma oportunidade de criar algo incrível. Não desista! 🚀💻',
            'A inovação nasce da curiosidade. Continue explorando e descobrindo! 🔍💭',
            'O conhecimento é o investimento que sempre retorna dividendos. Continue aprendendo! 📚🎓',
            'A colaboração multiplica o sucesso. Conecte-se e cresça junto! 🤝🌟'
        ];

        // Imagens de fundo disponíveis
        $backgroundImages = [
            'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146615/assets_task_01k0qsyhx4fbjbq7k8z19bxkw2_1753145794_img_0_aza4ph.webp',
            'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146672/662902ceb3ffcae10a826e3250ff7c4e_ox9tyq.jpg'
        ];

        return view('profiles.fortune-cookie', compact('profile', 'fortuneMessages', 'backgroundImages'));
    }

    public function updateBackground(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Perfil não encontrado'], 404);
        }

        $request->validate([
            'background_image_url' => 'required|url',
            'fortune_message' => 'nullable|string|max:500'
        ]);

        $profile->background_image_url = $request->background_image_url;
        
        if ($request->fortune_message) {
            $profile->fortune_cookie_message = $request->fortune_message;
        }

        $profile->save();

        return response()->json([
            'success' => true, 
            'message' => 'Imagem de fundo atualizada com sucesso!',
            'background_image_url' => $profile->background_image_url
        ]);
    }
}

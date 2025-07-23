<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLogin()
    {
        return view("auth.login");
    }

    /**
     * Processar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
            "remember" => ["nullable", "boolean"],
        ]);

        if (Auth::attempt($credentials, $request->boolean("remember"))) {
            $request->session()->regenerate();
            return redirect()->intended("/dashboard");
        }

        return back()->withErrors([
            "email" => "As credenciais não conferem com nossos registros.",
        ])->onlyInput("email");
    }

    /**
     * Mostrar formulário de registro
     */
    public function showRegister()
    {
        return view("auth.register");
    }

    /**
     * Processar registro
     */
    public function register(Request $request)
    {
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users"],
            "password" => ["required", "confirmed", Rules\Password::defaults()],
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect("/dashboard");
    }

    /**
     * Processar logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("/");
    }

    /**
     * Dashboard do usuário
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Buscar todas as atividades do usuário logado
        $atividades = method_exists($user, 'atividades') ? $user->atividades()->latest()->get() : collect();
        $atividadesCount = $atividades->count();
        $todos = collect();
        $projects = collect();

        return view("dashboard", compact("user", "atividades", "atividadesCount", "todos", "projects"));
    }

    /**
     * Perfil do usuário
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Buscar atividades dos últimos 30 dias
        $startDate = now()->subDays(30);
        $atividades = $user->atividades()
            ->where('created_at', '>=', $startDate)
            ->get();
        
        // Estatísticas gerais
        $totalAtividades = $atividades->count();
        $atividadesConcluidas = $atividades->where('status', 'concluida')->count();
        $atividadesNoPrazo = $atividades->filter(function($atividade) {
            return $atividade->status === 'concluida' && 
                   ($atividade->data_limite === null || 
                    $atividade->updated_at <= $atividade->data_limite);
        })->count();
        
        // Estatísticas por prioridade
        $prioridadeAlta = $atividades->where('prioridade', 'alta')->count();
        $prioridadeMedia = $atividades->where('prioridade', 'media')->count();
        $prioridadeBaixa = $atividades->where('prioridade', 'baixa')->count();
        
        // Estatísticas por status
        $statusPendente = $atividades->where('status', 'pendente')->count();
        $statusEmAndamento = $atividades->where('status', 'em_andamento')->count();
        $statusConcluida = $atividades->where('status', 'concluida')->count();
        
        // Atividades por dia (últimos 7 dias)
        $atividadesPorDia = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $dataFormatada = $data->format('Y-m-d');
            $count = $atividades->filter(function($atividade) use ($dataFormatada) {
                return $atividade->created_at->format('Y-m-d') === $dataFormatada;
            })->count();
            
            $atividadesPorDia[] = [
                'data' => $data->format('d/m'),
                'count' => $count
            ];
        }
        
        return view("auth.profile", compact(
            "user", 
            "atividades",
            "totalAtividades",
            "atividadesConcluidas", 
            "atividadesNoPrazo",
            "prioridadeAlta",
            "prioridadeMedia", 
            "prioridadeBaixa",
            "statusPendente",
            "statusEmAndamento",
            "statusConcluida",
            "atividadesPorDia"
        ));
    }

    /**
     * Atualizar perfil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users,email," . $user->id],
        ]);

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
        ]);

        return redirect()->route("profile")->with("success", "Perfil atualizado com sucesso!");
    }
}

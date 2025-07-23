<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DashMEBoard Neon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pattaya&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/glassmorphism.css') }}" rel="stylesheet">
    <style>
        /* Estilos específicos para produção */
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
            font-family: "Inter", sans-serif !important;
        }
        
        .glass-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-family: "Inter", sans-serif !important;
        }
        
        .glass-text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
            font-family: "Inter", sans-serif !important;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: "Space Grotesk", sans-serif !important;
        }
        
        body, p, span, div, a, button, input, textarea, select, label {
            font-family: "Inter", sans-serif !important;
        }
    </style>
</head>
<body class="dashboard-background">
    <div class="container my-4 glass-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="main-content-card">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x glass-icon mb-3" style="color: #10b981;"></i>
                        <h2 class="glass-text">🚀 Criar Conta Neon</h2>
                        <p class="glass-text-muted">Junte-se à nossa plataforma</p>
                    </div>
                    
                    @if ($errors->any())
                        <div class="glass-card p-3 mb-4" style="border-color: #ff6b6b; background: rgba(255, 107, 107, 0.1);">
                            @foreach ($errors->all() as $error)
                                <div class="glass-text" style="color: #ff6b6b;">{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="form-label glass-text">
                                <i class="fas fa-user me-2"></i>Nome Completo
                            </label>
                            <input type="text" class="form-control glass-input" id="name" name="name" 
                                   value="{{ old('name') }}" required placeholder="Seu nome">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label glass-text">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control glass-input" id="email" name="email" 
                                   value="{{ old('email') }}" required placeholder="seu@email.com">
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label glass-text">
                                <i class="fas fa-lock me-2"></i>Senha
                            </label>
                            <input type="password" class="form-control glass-input" id="password" 
                                   name="password" required placeholder="••••••••">
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label glass-text">
                                <i class="fas fa-shield-alt me-2"></i>Confirmar Senha
                            </label>
                            <input type="password" class="form-control glass-input" id="password_confirmation" 
                                   name="password_confirmation" required placeholder="••••••••">
                        </div>
                        
                        <button type="submit" class="btn glass-button w-100 mb-4">
                            <i class="fas fa-rocket me-2"></i>Criar Minha Conta
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <div class="glass-divider my-4"></div>
                        <p class="glass-text-muted mb-0">
                            Já tem uma conta? 
                            <a href="{{ route('login') }}" class="glass-link">
                                <i class="fas fa-sign-in-alt me-1"></i>Fazer login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
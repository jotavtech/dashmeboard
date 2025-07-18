<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DashMEBoard Neon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/glassmorphism.css') }}" rel="stylesheet">
</head>
<body class="dashboard-background">
    <div class="container my-4 glass-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="main-content-card">
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt fa-3x glass-icon mb-3" style="color: #00ffff;"></i>
                        <h2 class="glass-text">✨ Entrar no DashMEBoard</h2>
                                                  <p class="glass-text-muted">Acesse sua interface</p>
                    </div>
                    
                    @if ($errors->any())
                        <div class="glass-card p-3 mb-4" style="border-color: #ff6b6b; background: rgba(255, 107, 107, 0.1);">
                            @foreach ($errors->all() as $error)
                                <div class="glass-text" style="color: #ff6b6b;">{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
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
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input glass-checkbox" id="remember" name="remember">
                            <label class="form-check-label glass-text" for="remember">
                                <i class="fas fa-memory me-2"></i>Lembrar-me neste dispositivo
                            </label>
                        </div>
                        
                        <button type="submit" class="btn glass-button w-100 mb-4">
                            <i class="fas fa-rocket me-2"></i>Acessar Dashboard
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <div class="glass-divider my-4"></div>
                        <p class="glass-text-muted mb-0">
                            Novo por aqui? 
                            <a href="{{ route('register') }}" class="glass-link">
                                <i class="fas fa-user-plus me-1"></i>Criar conta
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
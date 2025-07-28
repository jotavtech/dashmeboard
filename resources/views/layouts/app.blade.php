<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DashMEBoard Neon')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pattaya&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/glassmorphism.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body class="dashboard-background">
    @auth
    <!-- Navegação -->
    <nav class="navbar navbar-expand-lg glass-navbar">
        <div class="container">
            <a class="navbar-brand glass-text" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2 d-md-none"></i>DashMEBoard
            </a>
            
            <button class="navbar-toggler glass-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1 d-md-none"></i><span class="d-none d-md-inline">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('atividades') }}">
                            <i class="fas fa-tasks me-1 d-md-none"></i><span class="d-none d-md-inline">Atividades</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('profiles.index') }}">
                            <i class="fas fa-users me-1 d-md-none"></i><span class="d-none d-md-inline">Descobrir</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('messages.conversations') }}">
                            <i class="fas fa-comments me-1 d-md-none"></i><span class="d-none d-md-inline">Conversas</span>
                            <span id="unread-count" class="badge bg-warning text-dark ms-1" style="display: none;"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('messages.index') }}">
                            <i class="fas fa-envelope me-1 d-md-none"></i><span class="d-none d-md-inline">Mensagens</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link glass-text" href="{{ route('agenda.index') }}">
                            <i class="fas fa-calendar-alt me-1 d-md-none"></i><span class="d-none d-md-inline">Agenda</span>
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle glass-text" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1 d-md-none"></i>{{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dark-dropdown">
                            <li>
                                <a class="dropdown-item dark-dropdown-item" href="{{ route('profiles.my-profile') }}">
                                    <i class="fas fa-user me-2"></i>Meu Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dark-dropdown-item" href="{{ route('profiles.edit') }}">
                                    <i class="fas fa-edit me-2"></i>Editar Perfil
                                </a>
                            </li>
                            <li id="fortuneCookieMenuItem" style="display: none;">
                                <a class="dropdown-item dark-dropdown-item" href="{{ route('fortune-cookie') }}">
                                    <i class="fas fa-cookie-bite me-2"></i>Biscoito da Sorte
                                </a>
                            </li>
                            <li><hr class="dropdown-divider dark-divider"></li>
                            <li>
                                <a class="dropdown-item dark-dropdown-item" href="{{ route('messages.create') }}">
                                    <i class="fas fa-edit me-2"></i>Nova Mensagem
                                </a>
                            </li>
                            <li><hr class="dropdown-divider dark-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item dark-dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Conteúdo Principal -->
    <main class="main-content">
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success glass-card" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger glass-card" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="container mt-3">
                <div class="alert alert-info glass-card" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @auth
    <script>
    // Verificar mensagens não lidas
    function checkUnreadMessages() {
        fetch('{{ route("messages.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('unread-count');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            });
    }

    // Verificar a cada 30 segundos
    checkUnreadMessages();
    setInterval(checkUnreadMessages, 30000);
    </script>
    @endauth

    @yield('scripts')
    
    @auth
    <script>
    // Aplicar imagem de fundo escolhida pelo usuário permanentemente
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        
        // Verificar se há plano de fundo salvo no localStorage
        const savedBackground = localStorage.getItem('userBackgroundImage');
        
        if (savedBackground) {
            // Aplicar o plano de fundo escolhido pelo usuário
            document.body.style.backgroundImage = `url(${savedBackground})`;
            document.body.style.backgroundSize = 'cover';
            document.body.style.backgroundPosition = 'center';
            document.body.style.backgroundAttachment = 'fixed';
        } else {
            // Verificar se o usuário tem uma imagem de fundo no perfil
            @if(auth()->user()->profile && auth()->user()->profile->background_image_url)
                const profileBackground = '{{ auth()->user()->profile->background_image_url }}';
                if (profileBackground) {
                    document.body.style.backgroundImage = `url(${profileBackground})`;
                    document.body.style.backgroundSize = 'cover';
                    document.body.style.backgroundPosition = 'center';
                    document.body.style.backgroundAttachment = 'fixed';
                    localStorage.setItem('userBackgroundImage', profileBackground);
                }
            @endif
        }
        
        // Se estiver visualizando o perfil de outro usuário, sobrescrever com o plano de fundo dele
        if (currentPath.includes('/profiles/') && !currentPath.includes('/my-profile') && !currentPath.includes('/edit')) {
            // O plano de fundo do perfil visitado será aplicado via JavaScript específico da página
        }

        // Controlar exibição do item do menu do biscoito da sorte
        const today = new Date().toDateString();
        const lastCookieDate = localStorage.getItem('lastFortuneCookieDate');
        const fortuneCookieMenuItem = document.getElementById('fortuneCookieMenuItem');
        
        // Mostrar item do menu apenas se não quebrou o biscoito hoje
        if (lastCookieDate !== today) {
            fortuneCookieMenuItem.style.display = 'block';
        }
    });
    </script>
    @endauth
</body>
</html> 
@extends('layouts.app')

@section('content')
@if($profile->background_image_url)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Salvar o plano de fundo atual do usuário
    const currentBackground = document.body.style.backgroundImage;
    const currentBackgroundSize = document.body.style.backgroundSize;
    const currentBackgroundPosition = document.body.style.backgroundPosition;
    const currentBackgroundAttachment = document.body.style.backgroundAttachment;
    
    // Salvar no localStorage para restaurar depois
    localStorage.setItem('userBackgroundImage', currentBackground);
    localStorage.setItem('userBackgroundSize', currentBackgroundSize);
    localStorage.setItem('userBackgroundPosition', currentBackgroundPosition);
    localStorage.setItem('userBackgroundAttachment', currentBackgroundAttachment);
    
    // Aplicar o plano de fundo do perfil sendo visualizado
    const profileBackground = '{{ $profile->background_image_url }}';
    if (profileBackground) {
        document.body.style.backgroundImage = `url(${profileBackground})`;
        document.body.style.backgroundSize = 'cover';
        document.body.style.backgroundPosition = 'center';
        document.body.style.backgroundAttachment = 'fixed';
        document.body.style.backgroundRepeat = 'no-repeat';
        
        // Adicionar classe para estilos específicos
        document.body.classList.add('profile-background-active');
        
        // Adicionar overlay para melhorar legibilidade
        if (!document.getElementById('profile-background-overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'profile-background-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                z-index: -1;
                pointer-events: none;
            `;
            document.body.appendChild(overlay);
        }
    }
    
    // Restaurar o plano de fundo do usuário quando sair da página
    window.addEventListener('beforeunload', function() {
        restoreUserBackground();
    });
    
    // Também restaurar quando clicar em links de navegação
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && !e.target.href.includes('{{ $profile->username }}')) {
            restoreUserBackground();
        }
    });
    
    function restoreUserBackground() {
        const savedBackground = localStorage.getItem('userBackgroundImage');
        const savedSize = localStorage.getItem('userBackgroundSize');
        const savedPosition = localStorage.getItem('userBackgroundPosition');
        const savedAttachment = localStorage.getItem('userBackgroundAttachment');
        
        if (savedBackground) {
            document.body.style.backgroundImage = savedBackground;
            document.body.style.backgroundSize = savedSize || 'cover';
            document.body.style.backgroundPosition = savedPosition || 'center';
            document.body.style.backgroundAttachment = savedAttachment || 'fixed';
        }
        
        // Remover classe CSS
        document.body.classList.remove('profile-background-active');
        
        // Remover overlay
        const overlay = document.getElementById('profile-background-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
});
</script>
@endif

<style>
/* Estilos específicos para quando o perfil tem imagem de fundo */
.profile-background-active .glass-card {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(30px) !important;
    -webkit-backdrop-filter: blur(30px) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
}

.profile-background-active .glass-button {
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
}

.profile-background-active .glass-button:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
}

.profile-background-active .glass-button-secondary {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
}

.profile-background-active .glass-button-secondary:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
}

.profile-background-active .glass-text {
    color: rgba(255, 255, 255, 0.95) !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.profile-background-active .glass-text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.profile-background-active .glass-badge {
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: rgba(255, 255, 255, 0.9) !important;
}
</style>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Cabeçalho do Perfil -->
            @if($profile->background_image_url)
            <div class="glass-card p-3 mb-3" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-image me-2" style="color: rgba(255, 255, 255, 0.7);"></i>
                    <small class="glass-text-muted mb-0">
                        <i class="fas fa-eye me-1"></i>Visualizando o fundo personalizado de @{{ $profile->username }}
                    </small>
                </div>
            </div>
            @endif
            <div class="glass-card p-4 mb-4">
                <div class="d-flex align-items-center mb-4">
                    @if($profile->profile_image)
                        <img src="{{ $profile->profile_image_url }}" 
                             class="rounded-circle me-4" 
                             width="100" height="100" 
                             alt="{{ $profile->user->name }}">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-4" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-white fa-3x"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <h2 class="glass-text mb-1">{{ $profile->user->name }}</h2>
                        <p class="glass-text-muted mb-2">@{{ $profile->username }}</p>
                        @if($profile->profession)
                            <span class="glass-badge">
                                <i class="fas fa-briefcase me-1"></i>{{ $profile->profession }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('messages.create', $profile->username) }}" 
                           class="btn glass-button">
                            <i class="fas fa-envelope me-2"></i>Enviar Mensagem
                        </a>
                        @if(auth()->id() === $profile->user_id)
                            <a href="{{ route('profiles.edit') }}" class="btn glass-button-secondary">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Status do Dia -->
                <div class="row mb-4">
                    @if($profile->mood)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-2">
                                <i class="fas fa-smile me-2"></i>Humor do dia
                            </h6>
                            <p class="glass-text-muted mb-0">{{ $profile->mood }}</p>
                        </div>
                    </div>
                    @endif

                    @if($profile->daily_music)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-2">
                                <i class="fas fa-music me-2"></i>Música do dia
                            </h6>
                            <p class="glass-text-muted mb-0">{{ $profile->daily_music }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Bio -->
                @if($profile->bio)
                <div class="glass-card p-3 mb-4">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-quote-left me-2"></i>Sobre
                    </h5>
                    <p class="glass-text-muted mb-0">{{ $profile->bio }}</p>
                </div>
                @endif

                <!-- Agenda Pública -->
                @if($profile->public_agenda)
                <div class="glass-card p-3 mb-4">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>O que está fazendo hoje
                    </h5>
                    <p class="glass-text-muted mb-0">{{ $profile->public_agenda }}</p>
                </div>
                @endif

                <!-- Atividades Públicas da Agenda -->
                @if($publicAgendaItems->count() > 0)
                <div class="glass-card p-3 mb-4">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-calendar-check me-2"></i>Próximas Atividades
                    </h5>
                    <div class="row">
                        @foreach($publicAgendaItems as $item)
                        <div class="col-12 mb-2">
                            <div class="d-flex align-items-center p-2" style="background: rgba(255, 255, 255, 0.05); border-radius: 8px; border-left: 4px solid {{ $item->color }};">
                                <div class="flex-grow-1">
                                    <h6 class="glass-text mb-1">{{ $item->title }}</h6>
                                    <small class="glass-text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ date('d/m/Y', strtotime($item->date)) }}
                                        @if($item->time)
                                            <i class="fas fa-clock ms-2 me-1"></i>{{ date('H:i', strtotime($item->time)) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Biscoito da Sorte -->
                @if($profile->fortune_cookie_message)
                <div class="glass-card p-4 mb-4 text-center" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1)); border: 2px solid rgba(255, 193, 7, 0.3);">
                    <div class="mb-3">
                        <i class="fas fa-cookie-bite fa-3x" style="color: #ffc107;"></i>
                    </div>
                    <h5 class="glass-text mb-3">🍪 Biscoito da Sorte</h5>
                    <div class="glass-card p-3" style="background: rgba(255, 255, 255, 0.1);">
                        <p class="glass-text mb-0 fst-italic">"{{ $profile->fortune_cookie_message }}"</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Navegação -->
            <div class="d-flex justify-content-center mb-4">
                <a href="{{ route('profiles.index') }}" class="btn glass-button-secondary me-3">
                    <i class="fas fa-arrow-left me-2"></i>Voltar aos Perfis
                </a>
                <a href="{{ route('profiles.search') }}" class="btn glass-button-secondary">
                    <i class="fas fa-search me-2"></i>Buscar Outros
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 
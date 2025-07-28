@extends('layouts.app')

@section('styles')
<style>
/* Estilos para os filtros de busca */
.search-filters-container {
    background: transparent !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px);
}

.search-input-group {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.search-input-group:focus-within {
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
}

.search-input-group .input-group-text {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.8);
}

.search-input-group .form-control {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.9);
    box-shadow: none;
}

.search-input-group .form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-input-group .form-control:focus {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.9);
    box-shadow: none;
}

.search-select {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.search-select:focus {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.9);
}

.search-select option {
    background: rgba(0, 0, 0, 0.8);
    color: rgba(255, 255, 255, 0.9);
}

.search-button {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.search-button:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: rgba(255, 255, 255, 1);
}

/* Responsividade */
@media (max-width: 768px) {
    .search-filters-row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-filters-row > div {
        min-width: 100% !important;
    }
}
</style>
@endsection

@section('content')
<div class="container my-4 glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 glass-text mb-2">
                                            <i class="fas fa-users me-2"></i>Descobrir
                </h1>
                <p class="glass-text-muted mb-0">Conecte-se com outros usuários e descubra perfis interessantes</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('profiles.my-profile') }}" class="btn btn-glass">
                    <i class="fas fa-user me-2"></i>Meu Perfil
                </a>
            </div>
        </div>

        <!-- Busca e Filtros -->
        <div class="glass-card mb-4 search-filters-container">
            <form action="{{ route('profiles.search') }}" method="GET">
                <div class="d-flex align-items-center gap-3 search-filters-row">
                    <div class="flex-grow-1">
                        <div class="input-group search-input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Buscar por nome, apelido, profissão ou bio..." 
                                   value="{{ request('q') }}">
                        </div>
                    </div>
                    <div style="min-width: 200px;">
                        <select class="form-select search-select" name="profession">
                            <option value="">Todas as profissões</option>
                            <option value="Desenvolvedor" {{ request('profession') == 'Desenvolvedor' ? 'selected' : '' }}>Desenvolvedor</option>
                            <option value="Designer" {{ request('profession') == 'Designer' ? 'selected' : '' }}>Designer</option>
                            <option value="Gerente" {{ request('profession') == 'Gerente' ? 'selected' : '' }}>Gerente</option>
                            <option value="Analista" {{ request('profession') == 'Analista' ? 'selected' : '' }}>Analista</option>
                            <option value="Estudante" {{ request('profession') == 'Estudante' ? 'selected' : '' }}>Estudante</option>
                        </select>
                    </div>
                    <div style="min-width: 120px;">
                        <button type="submit" class="btn search-button w-100">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $profiles->total() }}</h4>
                            <small class="glass-text-muted">Perfis Encontrados</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-briefcase text-success"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $profiles->where('profession', '!=', null)->count() }}</h4>
                            <small class="glass-text-muted">Com Profissão</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-image text-warning"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $profiles->where('profile_image', '!=', null)->count() }}</h4>
                            <small class="glass-text-muted">Com Foto</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-comments text-info"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $profiles->where('bio', '!=', null)->count() }}</h4>
                            <small class="glass-text-muted">Com Bio</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Perfis -->
        <div class="row">
            @forelse($profiles as $profile)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="profile-card">
                    <!-- Foto do usuário centralizada -->
                    <div class="profile-avatar-container">
                        @if($profile->profile_image)
                            <img src="{{ $profile->profile_image_url }}" 
                                 class="profile-image" 
                                 alt="{{ $profile->user->name }}">
                        @else
                            <div class="avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Informações do perfil em card -->
                    <div class="profile-info-card">
                        <!-- Nome e nickname -->
                        <div class="profile-header">
                            <h5 class="profile-name">{{ $profile->user->name }}</h5>
                            @if($profile->nickname)
                                <p class="profile-nickname">
                                    <i class="fas fa-tag me-1"></i>{{ $profile->nickname }}
                                </p>
                            @endif
                        </div>

                        <!-- Profissão -->
                        @if($profile->profession)
                        <div class="profile-profession">
                            <span class="profession-badge">
                                <i class="fas fa-briefcase me-1"></i>{{ $profile->profession }}
                            </span>
                        </div>
                        @endif

                        <!-- Mood -->
                        @if($profile->mood)
                        <div class="profile-mood">
                            <span class="mood-badge">
                                <i class="fas fa-smile me-1"></i>{{ $profile->mood }}
                            </span>
                        </div>
                        @endif

                        <!-- Bio -->
                        @if($profile->bio)
                        <div class="profile-bio">
                            <p>{{ Str::limit($profile->bio, 120) }}</p>
                        </div>
                        @endif

                        <!-- Agenda -->
                        @if($profile->public_agenda)
                        <div class="profile-agenda">
                            <div class="info-item">
                                <i class="fas fa-calendar-day"></i>
                                <span>{{ Str::limit($profile->public_agenda, 80) }}</span>
                            </div>
                        </div>
                        @endif

                        <!-- Música -->
                        @if($profile->daily_music)
                        <div class="profile-music">
                            <div class="info-item">
                                <i class="fas fa-music"></i>
                                <span>{{ $profile->daily_music }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Botões de ação -->
                    <div class="profile-actions">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('profiles.show', $profile->username) }}" 
                                   class="btn btn-glass w-100">
                                    <i class="fas fa-eye me-1"></i>Ver Perfil
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('messages.create', $profile->username) }}" 
                                   class="btn btn-glass-secondary w-100">
                                    <i class="fas fa-envelope me-1"></i>Mensagem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="glass-card text-center p-5">
                    <div class="empty-state">
                        <i class="fas fa-search fa-3x glass-text-muted mb-3"></i>
                        <h4 class="glass-text">Nenhum perfil encontrado</h4>
                        <p class="glass-text-muted">Tente ajustar sua busca ou explore outros perfis.</p>
                        <a href="{{ route('profiles.index') }}" class="btn glass-button mt-3">
                            <i class="fas fa-refresh me-2"></i>Ver Todos os Perfis
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($profiles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $profiles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 
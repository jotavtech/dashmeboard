@extends('layouts.app')

@section('content')
<div class="container my-4 glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 glass-text mb-2">
                    <i class="fas fa-search me-2"></i>Resultados da Busca
                </h1>
                <p class="glass-text-muted mb-0">
                    @if($query)
                        Resultados para: <strong>"{{ $query }}"</strong>
                    @else
                        Todos os perfis disponíveis
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('profiles.index') }}" class="btn btn-glass">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <!-- Busca e Filtros -->
        <div class="glass-card mb-4">
            <form action="{{ route('profiles.search') }}" method="GET">
                <div class="row g-3">
                    <div class="col-lg-8 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text glass-input">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control glass-input" 
                                   placeholder="Buscar por nome, apelido, profissão ou bio..." 
                                   value="{{ $query }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select class="form-select glass-input" name="profession">
                            <option value="">Todas as profissões</option>
                            <option value="Desenvolvedor" {{ $profession == 'Desenvolvedor' ? 'selected' : '' }}>Desenvolvedor</option>
                            <option value="Designer" {{ $profession == 'Designer' ? 'selected' : '' }}>Designer</option>
                            <option value="Gerente" {{ $profession == 'Gerente' ? 'selected' : '' }}>Gerente</option>
                            <option value="Analista" {{ $profession == 'Analista' ? 'selected' : '' }}>Analista</option>
                            <option value="Estudante" {{ $profession == 'Estudante' ? 'selected' : '' }}>Estudante</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <button type="submit" class="btn btn-glass w-100">
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
                        <p class="glass-text-muted">
                            @if($query)
                                Não encontramos perfis para "{{ $query }}". Tente ajustar sua busca.
                            @else
                                Não há perfis disponíveis no momento.
                            @endif
                        </p>
                        <a href="{{ route('profiles.index') }}" class="btn btn-glass mt-3">
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
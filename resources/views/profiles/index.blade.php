@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="glass-text mb-0">
                        <i class="fas fa-users me-2"></i>Descobrir Perfis
                    </h2>
                    <a href="{{ route('profiles.my-profile') }}" class="btn glass-button">
                        <i class="fas fa-user me-2"></i>Meu Perfil
                    </a>
                </div>

                <!-- Busca -->
                <form action="{{ route('profiles.search') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control glass-input" 
                               placeholder="Buscar por nome, profissão, bio..." 
                               value="{{ request('q') }}">
                        <button type="submit" class="btn glass-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Lista de Perfis -->
                <div class="row">
                    @forelse($profiles as $profile)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="glass-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if($profile->profile_image)
                                        <img src="{{ $profile->profile_image_url }}" 
                                             class="rounded-circle me-3" 
                                             width="60" height="60" 
                                             alt="{{ $profile->user->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="glass-text mb-1">{{ $profile->user->name }}</h5>
                                        <p class="glass-text-muted mb-0">@{{ $profile->username }}</p>
                                    </div>
                                </div>

                                @if($profile->profession)
                                <div class="mb-2">
                                    <span class="glass-badge">
                                        <i class="fas fa-briefcase me-1"></i>{{ $profile->profession }}
                                    </span>
                                </div>
                                @endif

                                @if($profile->mood)
                                <div class="mb-2">
                                    <span class="glass-badge">
                                        <i class="fas fa-smile me-1"></i>{{ $profile->mood }}
                                    </span>
                                </div>
                                @endif

                                @if($profile->bio)
                                <p class="glass-text-muted small mb-3">
                                    {{ Str::limit($profile->bio, 100) }}
                                </p>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ route('profiles.show', $profile->username) }}" 
                                       class="btn glass-button-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>Ver Perfil
                                    </a>
                                    <a href="{{ route('messages.create', $profile->username) }}" 
                                       class="btn glass-button-sm">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="glass-card text-center p-5">
                            <i class="fas fa-search fa-3x glass-text-muted mb-3"></i>
                            <h4 class="glass-text">Nenhum perfil encontrado</h4>
                            <p class="glass-text-muted">Tente ajustar sua busca ou explore outros perfis.</p>
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
    </div>
</div>
@endsection 
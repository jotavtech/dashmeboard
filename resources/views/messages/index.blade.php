@extends('layouts.app')

@section('content')
<div class="container my-4 glass-container">
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="glass-text mb-0">
                        <i class="fas fa-envelope me-2"></i>Mensagens
                    </h2>
                    <a href="{{ route('profiles.index') }}" class="btn glass-button">
                        <i class="fas fa-users me-2"></i>Encontrar Pessoas
                    </a>
                </div>

                <!-- Lista de Mensagens -->
                <div class="row">
                    @forelse($messages as $message)
                    <div class="col-12 mb-3">
                        <div class="glass-card p-3 {{ $message->receiver_id === auth()->id() && !$message->is_read ? 'border-warning' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if($message->sender_id === auth()->id())
                                            <span class="glass-badge me-2" style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">
                                                <i class="fas fa-paper-plane me-1"></i>Enviada
                                            </span>
                                            <strong class="glass-text">Para: {{ $message->receiver->name }}</strong>
                                        @else
                                            @if(!$message->is_read)
                                                <span class="glass-badge me-2" style="background: rgba(255, 193, 7, 0.2); color: #ffc107;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Nova
                                                </span>
                                            @endif
                                            <strong class="glass-text">De: {{ $message->sender->name }}</strong>
                                        @endif
                                    </div>
                                    
                                    <p class="glass-text-muted mb-2">
                                        {{ Str::limit($message->message, 150) }}
                                    </p>
                                    
                                    <small class="glass-text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $message->created_at->diffForHumans() }}
                                        @if($message->is_read && $message->read_at)
                                            <span class="ms-2">
                                                <i class="fas fa-check me-1"></i>Lida em {{ $message->read_at->format('d/m H:i') }}
                                            </span>
                                        @endif
                                    </small>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('messages.show', $message->id) }}" 
                                       class="btn glass-button-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($message->sender_id !== auth()->id())
                                        <a href="{{ route('messages.conversation', $message->sender->profile->username ?? 'user') }}" 
                                           class="btn glass-button-sm">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="glass-card text-center p-5">
                            <i class="fas fa-envelope fa-3x glass-text-muted mb-3"></i>
                            <h4 class="glass-text">Nenhuma mensagem</h4>
                            <p class="glass-text-muted">Você ainda não tem mensagens. Que tal conhecer novas pessoas?</p>
                            <a href="{{ route('profiles.index') }}" class="btn glass-button">
                                <i class="fas fa-users me-2"></i>Descobrir Perfis
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if($messages->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $messages->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
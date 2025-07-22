@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="glass-text mb-0">
                        <i class="fas fa-envelope me-2"></i>Mensagem
                    </h2>
                    <div class="d-flex gap-2">
                        @if($message->sender_id !== auth()->id())
                            <a href="{{ route('messages.conversation', $message->sender->profile->username ?? 'user') }}" 
                               class="btn glass-button">
                                <i class="fas fa-reply me-2"></i>Responder
                            </a>
                        @endif
                        <a href="{{ route('messages.index') }}" class="btn glass-button-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                </div>

                <!-- Detalhes da Mensagem -->
                <div class="glass-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        @if($message->sender_id === auth()->id())
                            <span class="glass-badge me-2" style="background: rgba(0, 123, 255, 0.2); color: #007bff;">
                                <i class="fas fa-paper-plane me-1"></i>Enviada
                            </span>
                            <strong class="glass-text">Para: {{ $message->receiver->name }}</strong>
                        @else
                            <span class="glass-badge me-2" style="background: rgba(40, 167, 69, 0.2); color: #28a745;">
                                <i class="fas fa-inbox me-1"></i>Recebida
                            </span>
                            <strong class="glass-text">De: {{ $message->sender->name }}</strong>
                        @endif
                    </div>

                    <div class="glass-card p-3 mb-3" style="background: rgba(255, 255, 255, 0.1);">
                        <p class="glass-text-muted mb-0">{{ $message->message }}</p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="glass-text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Enviada em {{ $message->created_at->format('d/m/Y \à\s H:i') }}
                        </small>
                        
                        @if($message->is_read && $message->read_at)
                            <small class="glass-text-muted">
                                <i class="fas fa-check me-1" style="color: #28a745;"></i>
                                Lida em {{ $message->read_at->format('d/m/Y \à\s H:i') }}
                            </small>
                        @elseif($message->receiver_id === auth()->id())
                            <small class="glass-text-muted">
                                <i class="fas fa-exclamation-circle me-1" style="color: #ffc107;"></i>
                                Não lida
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-flex gap-3">
                    @if($message->sender_id !== auth()->id())
                        <a href="{{ route('messages.conversation', $message->sender->profile->username ?? 'user') }}" 
                           class="btn glass-button flex-fill">
                            <i class="fas fa-comments me-2"></i>Iniciar Conversa
                        </a>
                    @endif
                    <a href="{{ route('messages.index') }}" class="btn glass-button-secondary">
                        <i class="fas fa-list me-2"></i>Todas as Mensagens
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
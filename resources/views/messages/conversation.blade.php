@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Cabeçalho da Conversa -->
            <div class="glass-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        @if($otherUser->profile && $otherUser->profile->profile_image)
                            <img src="{{ $otherUser->profile->profile_image_url }}" 
                                 class="rounded-circle me-3" 
                                 width="60" height="60" 
                                 alt="{{ $otherUser->name }}">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="glass-text mb-1">{{ $otherUser->name }}</h4>
                            @if($otherUser->profile)
                                <p class="glass-text-muted mb-0">@{{ $otherUser->profile->username }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('messages.create', $otherUser->profile->username ?? 'user') }}" 
                           class="btn glass-button">
                            <i class="fas fa-paper-plane me-2"></i>Nova Mensagem
                        </a>
                        <a href="{{ route('messages.index') }}" class="btn glass-button-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mensagens -->
            <div class="glass-card p-4 mb-4" style="height: 500px; overflow-y: auto;">
                @forelse($messages as $message)
                <div class="mb-3">
                    <div class="d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="glass-card p-3" style="max-width: 70%; {{ $message->sender_id === auth()->id() ? 'background: rgba(0, 123, 255, 0.1);' : 'background: rgba(255, 255, 255, 0.1);' }}">
                            <div class="d-flex align-items-center mb-2">
                                <strong class="glass-text me-2">
                                    {{ $message->sender_id === auth()->id() ? 'Você' : $message->sender->name }}
                                </strong>
                                <small class="glass-text-muted">
                                    {{ $message->created_at->format('d/m H:i') }}
                                    @if($message->is_read && $message->read_at)
                                        <i class="fas fa-check ms-1" style="color: #28a745;"></i>
                                    @endif
                                </small>
                            </div>
                            <p class="glass-text-muted mb-0">{{ $message->message }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x glass-text-muted mb-3"></i>
                    <h5 class="glass-text">Nenhuma mensagem ainda</h5>
                    <p class="glass-text-muted">Seja o primeiro a iniciar a conversa!</p>
                </div>
                @endforelse
            </div>

            <!-- Formulário de Nova Mensagem -->
            <div class="glass-card p-4">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="to_user_id" value="{{ $otherUser->id }}">
                    
                    <div class="d-flex gap-3">
                        <div class="flex-grow-1">
                            <textarea class="form-control glass-input" name="message" 
                                      rows="3" placeholder="Digite sua mensagem..." required></textarea>
                        </div>
                        <button type="submit" class="btn glass-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll para a última mensagem
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.querySelector('.glass-card[style*="overflow-y: auto"]');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>
@endsection 
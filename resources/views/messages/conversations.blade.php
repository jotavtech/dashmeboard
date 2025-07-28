@extends('layouts.app')

@section('title', 'Conversas - DashMEBoard Neon')

@section('content')
<div class="container my-4 glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 glass-text mb-2">
                    <i class="fas fa-comments me-2"></i>Minhas Conversas
                </h1>
                <p class="glass-text-muted mb-0">Gerencie suas conversas e mensagens</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('messages.create') }}" class="btn btn-glass">
                        <i class="fas fa-edit me-2"></i>Nova Mensagem
                    </a>
                    <a href="{{ route('messages.index') }}" class="btn btn-glass-secondary">
                        <i class="fas fa-list me-2"></i>Todas as Mensagens
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-comments text-primary"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $conversations->count() }}</h4>
                            <small class="glass-text-muted">Conversas</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-envelope text-warning"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $conversations->sum('unread_count') }}</h4>
                            <small class="glass-text-muted">Não Lidas</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-comment-dots text-success"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $conversations->sum('total_messages') }}</h4>
                            <small class="glass-text-muted">Total Mensagens</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0">{{ $conversations->where('last_activity', '>=', now()->subDays(7))->count() }}</h4>
                            <small class="glass-text-muted">Ativas (7d)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="glass-card mb-4">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label glass-text">
                        <i class="fas fa-filter me-1"></i>Filtrar por
                    </label>
                    <select class="form-select glass-input" id="conversationFilter">
                        <option value="all">Todas as conversas</option>
                        <option value="unread">Com mensagens não lidas</option>
                        <option value="recent">Ativas (últimos 7 dias)</option>
                        <option value="old">Antigas (mais de 7 dias)</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label glass-text">
                        <i class="fas fa-sort me-1"></i>Ordenar por
                    </label>
                    <select class="form-select glass-input" id="conversationSort">
                        <option value="activity">Última atividade</option>
                        <option value="unread">Mensagens não lidas</option>
                        <option value="name">Nome do usuário</option>
                        <option value="messages">Total de mensagens</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-8">
                    <label class="form-label glass-text">
                        <i class="fas fa-search me-1"></i>Buscar conversa
                    </label>
                    <input type="text" class="form-control glass-input" id="conversationSearch" 
                           placeholder="Buscar por nome do usuário...">
                </div>
                <div class="col-lg-2 col-md-4 d-flex align-items-end">
                    <button class="btn btn-glass w-100" onclick="applyConversationFilters()">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Conversas -->
        <div class="conversations-list" id="conversationsContainer">
            @forelse($conversations as $conversation)
            <div class="conversation-card" data-conversation="{{ json_encode($conversation) }}">
                <div class="conversation-avatar">
                    @if($conversation['user']->profile && $conversation['user']->profile->profile_image)
                        <img src="{{ $conversation['user']->profile->profile_image_url }}" 
                             class="rounded-circle" 
                             width="60" height="60" 
                             alt="{{ $conversation['user']->name }}">
                    @else
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    @if($conversation['unread_count'] > 0)
                        <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
                    @endif
                </div>
                
                <div class="conversation-content">
                    <div class="conversation-header">
                        <h6 class="glass-text mb-1">{{ $conversation['user']->name }}</h6>
                        <small class="glass-text-muted">
                            @if($conversation['user']->profile)
                                @{{ $conversation['user']->profile->username }}
                            @else
                                Usuário
                            @endif
                        </small>
                    </div>
                    
                    <div class="conversation-preview">
                        <p class="glass-text-muted mb-1">
                            @if($conversation['latest_message']->sender_id === auth()->id())
                                <i class="fas fa-reply me-1"></i>
                            @else
                                <i class="fas fa-envelope me-1"></i>
                            @endif
                            {{ Str::limit($conversation['latest_message']->message, 80) }}
                        </p>
                    </div>
                    
                    <div class="conversation-meta">
                        <small class="glass-text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $conversation['last_activity']->diffForHumans() }}
                        </small>
                        <small class="glass-text-muted ms-3">
                            <i class="fas fa-comment me-1"></i>
                            {{ $conversation['total_messages'] }} mensagens
                        </small>
                    </div>
                </div>
                
                <div class="conversation-actions">
                    <a href="{{ route('messages.conversation', $conversation['user']->profile ? $conversation['user']->profile->username : 'user' . $conversation['user']->id) }}" 
                       class="btn glass-button-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="empty-conversations">
                <div class="text-center p-5">
                    <i class="fas fa-comments fa-3x glass-text-muted mb-3"></i>
                    <h4 class="glass-text">Nenhuma conversa encontrada</h4>
                    <p class="glass-text-muted">Comece uma conversa enviando uma mensagem para alguém.</p>
                    <div class="mt-4">
                        <a href="{{ route('messages.create') }}" class="btn btn-glass me-2">
                            <i class="fas fa-edit me-2"></i>Nova Mensagem
                        </a>
                        <a href="{{ route('profiles.index') }}" class="btn btn-glass-secondary">
                            <i class="fas fa-users me-2"></i>Descobrir
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
// Inicialização segura das variáveis
let allConversations = [];

// Tentar carregar conversas do PHP, se disponível
try {
    @if(isset($conversations))
        allConversations = @json($conversations);
    @endif
} catch (e) {
    console.log('Conversas não disponíveis via PHP, usando array vazio');
    allConversations = [];
}

// Aplicar filtros de conversas
function applyConversationFilters() {
    const filter = document.getElementById('conversationFilter').value;
    const sort = document.getElementById('conversationSort').value;
    const search = document.getElementById('conversationSearch').value.toLowerCase();
    
    let filtered = [...allConversations];
    
    // Aplicar filtros
    if (filter === 'unread') {
        filtered = filtered.filter(c => c.unread_count > 0);
    } else if (filter === 'recent') {
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        filtered = filtered.filter(c => new Date(c.last_activity) >= weekAgo);
    } else if (filter === 'old') {
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        filtered = filtered.filter(c => new Date(c.last_activity) < weekAgo);
    }
    
    // Aplicar busca
    if (search) {
        filtered = filtered.filter(c => 
            c.user.name.toLowerCase().includes(search) ||
            (c.user.profile && c.user.profile.username && c.user.profile.username.toLowerCase().includes(search))
        );
    }
    
    // Aplicar ordenação
    filtered.sort((a, b) => {
        switch (sort) {
            case 'activity':
                return new Date(b.last_activity) - new Date(a.last_activity);
            case 'unread':
                return b.unread_count - a.unread_count;
            case 'name':
                return a.user.name.localeCompare(b.user.name);
            case 'messages':
                return b.total_messages - a.total_messages;
            default:
                return 0;
        }
    });
    
    renderConversations(filtered);
}

// Renderizar conversas filtradas
function renderConversations(conversations) {
    const container = document.getElementById('conversationsContainer');
    
    if (conversations.length === 0) {
        container.innerHTML = `
            <div class="empty-conversations">
                <div class="text-center p-5">
                    <i class="fas fa-search fa-3x glass-text-muted mb-3"></i>
                    <h4 class="glass-text">Nenhuma conversa encontrada</h4>
                    <p class="glass-text-muted">Tente ajustar os filtros ou buscar por outro termo.</p>
                </div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = conversations.map(conversation => `
        <div class="conversation-card" onclick="window.location.href='{{ route('messages.conversation', ':username') }}'.replace(':username', conversation.user.profile ? conversation.user.profile.username : 'user' + conversation.user.id)">
            <div class="conversation-avatar">
                ${conversation.user.profile && conversation.user.profile.profile_image ? 
                    `<img src="${conversation.user.profile.profile_image_url}" class="rounded-circle" width="60" height="60" alt="${conversation.user.name}">` :
                    `<div class="avatar-placeholder"><i class="fas fa-user"></i></div>`
                }
                ${conversation.unread_count > 0 ? `<span class="unread-badge">${conversation.unread_count}</span>` : ''}
            </div>
            
            <div class="conversation-content">
                <div class="conversation-header">
                    <h6 class="glass-text mb-1">${conversation.user.name}</h6>
                    <small class="glass-text-muted">
                        ${conversation.user.profile ? '@' + conversation.user.profile.username : 'Usuário'}
                    </small>
                </div>
                
                <div class="conversation-preview">
                    <p class="glass-text-muted mb-1">
                        ${conversation.latest_message.sender_id === {{ auth()->id() }} ? 
                            '<i class="fas fa-reply me-1"></i>' : 
                            '<i class="fas fa-envelope me-1"></i>'
                        }
                        ${conversation.latest_message.message.length > 80 ? 
                            conversation.latest_message.message.substring(0, 80) + '...' : 
                            conversation.latest_message.message
                        }
                    </p>
                </div>
                
                <div class="conversation-meta">
                    <small class="glass-text-muted">
                        <i class="fas fa-clock me-1"></i>
                        ${new Date(conversation.last_activity).toLocaleDateString()}
                    </small>
                    <small class="glass-text-muted ms-3">
                        <i class="fas fa-comment me-1"></i>
                        ${conversation.total_messages} mensagens
                    </small>
                </div>
            </div>
            
            <div class="conversation-actions">
                <a href="{{ route('messages.conversation', ':username') }}".replace(':username', conversation.user.profile ? conversation.user.profile.username : 'user' + conversation.user.id) 
                   class="btn glass-button-sm">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
    `).join('');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('conversationFilter').addEventListener('change', applyConversationFilters);
    document.getElementById('conversationSort').addEventListener('change', applyConversationFilters);
    document.getElementById('conversationSearch').addEventListener('input', applyConversationFilters);
});
</script>
@endsection 
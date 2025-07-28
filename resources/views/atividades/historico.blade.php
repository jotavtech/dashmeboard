@extends('layouts.app')

@section('title', 'Histórico de Atividades - DashMEBoard')

@section('content')
<div class="container my-4 glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 glass-text">
                            <i class="fas fa-history me-2" style="color: #8b5cf6;"></i>
                            Histórico de Atividades
                        </h1>
                        <p class="glass-text-muted">Atividades concluídas e arquivadas</p>
                    </div>
                    <div>
                        <a href="{{ route('atividades') }}" class="btn btn-glass">
                            <i class="fas fa-arrow-left me-2"></i>Voltar às Atividades
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="glass-card p-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="searchInput" class="form-label glass-text">Buscar</label>
                            <input type="text" class="form-control glass-input" id="searchInput" placeholder="Buscar atividades...">
                        </div>
                        <div class="col-md-3">
                            <label for="priorityFilter" class="form-label glass-text">Prioridade</label>
                            <select class="form-select glass-input" id="priorityFilter">
                                <option value="">Todas</option>
                                <option value="baixa">Baixa</option>
                                <option value="media">Média</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="periodFilter" class="form-label glass-text">Período</label>
                            <select class="form-select glass-input" id="periodFilter">
                                <option value="">Todos</option>
                                <option value="today">Hoje</option>
                                <option value="week">Esta Semana</option>
                                <option value="month">Este Mês</option>
                                <option value="year">Este Ano</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sortFilter" class="form-label glass-text">Ordenar</label>
                            <select class="form-select glass-input" id="sortFilter">
                                <option value="archived_at">Data de Arquivamento</option>
                                <option value="completed_at">Data de Conclusão</option>
                                <option value="created_at">Data de Criação</option>
                                <option value="titulo">Título</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="stats-grid">
                    <div class="stats-card text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1 glass-text">{{ $atividades->count() }}</h3>
                                <p class="mb-0 glass-text-muted">Total Arquivadas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-archive fa-2x" style="color: #8b5cf6; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1 glass-text">{{ $atividades->where('prioridade', 'alta')->count() }}</h3>
                                <p class="mb-0 glass-text-muted">Alta Prioridade</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-arrow-up fa-2x" style="color: #ef4444; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1 glass-text">{{ $atividades->where('prioridade', 'media')->count() }}</h3>
                                <p class="mb-0 glass-text-muted">Média Prioridade</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-minus fa-2x" style="color: #f59e0b; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1 glass-text">{{ $atividades->where('prioridade', 'baixa')->count() }}</h3>
                                <p class="mb-0 glass-text-muted">Baixa Prioridade</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-arrow-down fa-2x" style="color: #06b6d4; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Atividades -->
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="card-body">
                        <div id="atividadesContainer">
                            @if($atividades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Prioridade</th>
                                                <th>Progresso</th>
                                                <th>Concluída em</th>
                                                <th>Arquivada em</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($atividades as $atividade)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $atividade->titulo }}</strong>
                                                        <br><small class="glass-text-muted">{{ Str::limit($atividade->descricao, 100) }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="glass-badge priority-{{ $atividade->prioridade }}">
                                                            {{ ucfirst($atividade->prioridade) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress flex-grow-1" style="min-width: 100px;">
                                                                <div class="progress-bar" style="width: {{ $atividade->progresso ?? 0 }}%"></div>
                                                            </div>
                                                            <small class="me-2">{{ $atividade->progresso ?? 0 }}%</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($atividade->completed_at)
                                                            <small class="glass-text-muted">
                                                                {{ $atividade->completed_at->format('d/m/Y H:i') }}
                                                            </small>
                                                        @else
                                                            <small class="glass-text-muted">-</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($atividade->archived_at)
                                                            <small class="glass-text-muted">
                                                                {{ $atividade->archived_at->format('d/m/Y H:i') }}
                                                            </small>
                                                        @else
                                                            <small class="glass-text-muted">-</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-glass-secondary btn-sm" onclick="restoreActivity({{ $atividade->id }})" title="Restaurar">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button class="btn btn-glass-danger btn-sm" onclick="deleteActivity({{ $atividade->id }})" title="Excluir Permanentemente">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-archive fa-4x glass-text-muted mb-3"></i>
                                    <h4 class="glass-text mb-3">Nenhuma atividade arquivada</h4>
                                    <p class="glass-text-muted mb-4">Atividades concluídas aparecerão aqui quando forem movidas para o histórico.</p>
                                    <a href="{{ route('atividades') }}" class="btn btn-glass">
                                        <i class="fas fa-arrow-left me-2"></i>Voltar às Atividades
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
let atividades = @json($atividades);

// Função para restaurar atividade
function restoreActivity(id) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    if (!confirm(`Deseja restaurar "${atividade.titulo}" para a lista de atividades ativas?`)) {
        return;
    }

    fetch(`/restore-activity/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover da lista de atividades arquivadas
            atividades = atividades.filter(a => a.id !== id);
            
            // Atualizar interface
            applyFilters();
            
            // Mostrar notificação
            showNotification('Atividade restaurada com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao restaurar atividade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao restaurar atividade:', error);
        showNotification('Erro ao restaurar atividade', 'error');
    });
}

// Função para excluir permanentemente
function deleteActivity(id) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    if (!confirm(`Deseja excluir permanentemente "${atividade.titulo}"? Esta ação não pode ser desfeita.`)) {
        return;
    }

    fetch(`/delete-activity/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover da lista
            atividades = atividades.filter(a => a.id !== id);
            
            // Atualizar interface
            applyFilters();
            
            // Mostrar notificação
            showNotification('Atividade excluída permanentemente!', 'success');
        } else {
            showNotification(data.message || 'Erro ao excluir atividade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir atividade:', error);
        showNotification('Erro ao excluir atividade', 'error');
    });
}

// Aplicar filtros
function applyFilters() {
    const priorityFilter = document.getElementById('priorityFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    const periodFilter = document.getElementById('periodFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();

    let filtered = [...atividades];

    // Aplicar filtros de período
    if (periodFilter === 'today') {
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        filtered = filtered.filter(a => new Date(a.archived_at) >= todayStart);
    } else if (periodFilter === 'week') {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        filtered = filtered.filter(a => new Date(a.archived_at) >= startOfWeek);
    } else if (periodFilter === 'month') {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        filtered = filtered.filter(a => new Date(a.archived_at) >= startOfMonth);
    } else if (periodFilter === 'year') {
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        filtered = filtered.filter(a => new Date(a.archived_at) >= startOfYear);
    }

    // Aplicar filtros de prioridade
    if (priorityFilter) {
        filtered = filtered.filter(a => a.prioridade === priorityFilter);
    }

    // Aplicar busca
    if (searchInput) {
        filtered = filtered.filter(a => 
            a.titulo.toLowerCase().includes(searchInput) ||
            (a.descricao && a.descricao.toLowerCase().includes(searchInput))
        );
    }

    // Ordenar
    filtered.sort((a, b) => {
        switch (sortFilter) {
            case 'archived_at':
                return new Date(b.archived_at) - new Date(a.archived_at);
            case 'completed_at':
                return new Date(b.completed_at) - new Date(a.completed_at);
            case 'created_at':
                return new Date(b.created_at) - new Date(a.created_at);
            case 'titulo':
                return a.titulo.localeCompare(b.titulo);
            default:
                return 0;
        }
    });

    renderHistorico(filtered);
}

// Renderizar histórico
function renderHistorico(atividades) {
    const container = document.getElementById('atividadesContainer');
    
    if (atividades.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x glass-text-muted mb-3"></i>
                <h4 class="glass-text mb-3">Nenhuma atividade encontrada</h4>
                <p class="glass-text-muted">Tente ajustar os filtros de busca.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Prioridade</th>
                        <th>Progresso</th>
                        <th>Concluída em</th>
                        <th>Arquivada em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    ${atividades.map(atividade => `
                        <tr>
                            <td>
                                <strong>${atividade.titulo}</strong>
                                <br><small class="glass-text-muted">${atividade.descricao ? atividade.descricao.substring(0, 100) + (atividade.descricao.length > 100 ? '...' : '') : 'Sem descrição'}</small>
                            </td>
                            <td>
                                <span class="glass-badge priority-${atividade.prioridade}">
                                    ${atividade.prioridade.charAt(0).toUpperCase() + atividade.prioridade.slice(1)}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="min-width: 100px;">
                                        <div class="progress-bar" style="width: ${atividade.progresso || 0}%"></div>
                                    </div>
                                    <small class="me-2">${atividade.progresso || 0}%</small>
                                </div>
                            </td>
                            <td>
                                <small class="glass-text-muted">
                                    ${atividade.completed_at ? new Date(atividade.completed_at).toLocaleDateString('pt-BR') + ' ' + new Date(atividade.completed_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '-'}
                                </small>
                            </td>
                            <td>
                                <small class="glass-text-muted">
                                    ${atividade.archived_at ? new Date(atividade.archived_at).toLocaleDateString('pt-BR') + ' ' + new Date(atividade.archived_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '-'}
                                </small>
                            </td>
                            <td>
                                <button class="btn btn-glass-secondary btn-sm" onclick="restoreActivity(${atividade.id})" title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button class="btn btn-glass-danger btn-sm" onclick="deleteActivity(${atividade.id})" title="Excluir Permanentemente">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Notificações
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `glass-notification ${type}`;
    notification.innerHTML = `
        <div class="glass-card p-3">
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'} me-2"></i>
                <span class="glass-text">${message}</span>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Event listeners para filtros
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('priorityFilter').addEventListener('change', applyFilters);
document.getElementById('periodFilter').addEventListener('change', applyFilters);
document.getElementById('sortFilter').addEventListener('change', applyFilters);
</script>
@endsection 
@extends('layouts.app')

@section('title', 'Atividades - DashMEBoard Neon')

@section('content')
<div class="container my-4 glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 glass-text mb-2">
                    <i class="fas fa-tasks me-2"></i>Minhas Atividades
                </h1>
                <p class="glass-text-muted mb-0">Gerencie suas atividades e mantenha o controle dos seus projetos</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('atividades.historico') }}" class="btn btn-glass-secondary me-2">
                    <i class="fas fa-history me-2"></i>Histórico
                </a>
                <button class="btn btn-glass" onclick="openCreateModal()">
                    <i class="fas fa-plus me-2"></i>Nova Atividade
                </button>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0" id="totalAtividades">0</h4>
                            <small class="glass-text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0" id="pendentes">0</h4>
                            <small class="glass-text-muted">Pendentes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-spinner text-info"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0" id="emAndamento">0</h4>
                            <small class="glass-text-muted">Em Andamento</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3">
                            <i class="fas fa-check text-success"></i>
                        </div>
                        <div>
                            <h4 class="glass-text mb-0" id="concluidas">0</h4>
                            <small class="glass-text-muted">Concluídas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de Navegação -->
        <div class="glass-card mb-4">
            <ul class="nav nav-tabs" id="activitiesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">
                        <i class="fas fa-tasks me-2"></i>Atividades Ativas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                        <i class="fas fa-history me-2"></i>Histórico
                    </button>
                </li>
            </ul>
        </div>

        <!-- Conteúdo das Tabs -->
        <div class="tab-content" id="activitiesTabContent">
            <!-- Tab Atividades Ativas -->
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                <!-- Filtros -->
                <div class="glass-card mb-4">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <select id="statusFilter" class="form-select filter-input" onchange="applyFilters()">
                                <option value="">Todos os Status</option>
                                <option value="pendente">Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluida">Concluída</option>
                            </select>
                        </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select id="priorityFilter" class="form-select filter-input" onchange="applyFilters()">
                        <option value="">Todas as Prioridades</option>
                        <option value="alta">Alta</option>
                        <option value="media">Média</option>
                        <option value="baixa">Baixa</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select id="periodFilter" class="form-select filter-input" onchange="applyFilters()">
                        <option value="">Todos os Períodos</option>
                        <option value="today">Hoje</option>
                        <option value="week">Esta Semana</option>
                        <option value="month">Este Mês</option>
                        <option value="overdue">Atrasadas</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select id="sortFilter" class="form-select filter-input" onchange="applyFilters()">
                        <option value="created_at">Data de Criação</option>
                        <option value="data_fim">Data de Conclusão</option>
                        <option value="prioridade">Prioridade</option>
                        <option value="status">Status</option>
                        <option value="progresso">Progresso</option>
                        <option value="titulo">Título</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select id="viewFilter" class="form-select filter-input" onchange="changeView()">
                        <option value="cards">Cards</option>
                        <option value="list">Lista</option>
                        <option value="compact">Compacto</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <input type="text" id="searchInput" class="form-control filter-input" 
                           placeholder="Buscar atividades..." onkeyup="applyFilters()">
                </div>
            </div>
        </div>

        <!-- Lista de Atividades -->
        <div id="atividadesContainer">
            <!-- As atividades serão renderizadas aqui via JavaScript -->
        </div>
            </div>

            <!-- Tab Histórico -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <!-- Filtros do Histórico -->
                <div class="glass-card mb-4">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <select id="historyStatusFilter" class="form-select filter-input" onchange="applyHistoryFilters()">
                                <option value="">Todos os Status</option>
                                <option value="concluida">Concluída</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <select id="historyPeriodFilter" class="form-select filter-input" onchange="applyHistoryFilters()">
                                <option value="">Todos os Períodos</option>
                                <option value="today">Hoje</option>
                                <option value="week">Esta Semana</option>
                                <option value="month">Este Mês</option>
                                <option value="year">Este Ano</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <select id="historySortFilter" class="form-select filter-input" onchange="applyHistoryFilters()">
                                <option value="completed_at">Data de Conclusão</option>
                                <option value="created_at">Data de Criação</option>
                                <option value="titulo">Título</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <input type="text" id="historySearchInput" class="form-control filter-input" 
                                   placeholder="Buscar no histórico..." onkeyup="applyHistoryFilters()">
                        </div>
                    </div>
                </div>

                <!-- Lista do Histórico -->
                <div id="historyContainer">
                    <!-- O histórico será renderizado aqui via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay de Edição -->
<div id="editOverlay" class="edit-overlay">
    <div class="edit-overlay-background"></div>
    <div class="edit-container">
        <div id="editHighlightCard" class="edit-highlight-card">
            <!-- Card da atividade será clonado aqui -->
        </div>
        <div class="edit-form-container">
            <div class="edit-form-card">
                <div class="edit-form-header">
                    <h5><i class="fas fa-edit me-2"></i>Editar Atividade</h5>
                    <button class="edit-close-btn" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="edit-form-body">
                    <form id="editForm">
                        <input type="hidden" id="editId">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label glass-text">Título</label>
                                <input type="text" id="editTitulo" class="form-control glass-input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Status</label>
                                <select id="editStatus" class="form-select glass-input">
                                    <option value="pendente">Pendente</option>
                                    <option value="em_andamento">Em Andamento</option>
                                    <option value="concluida">Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Prioridade</label>
                                <select id="editPrioridade" class="form-select glass-input">
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Data de Início</label>
                                <input type="date" id="editDataInicio" class="form-control glass-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Data de Conclusão</label>
                                <input type="date" id="editDataFim" class="form-control glass-input">
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Descrição</label>
                                <textarea id="editDescricao" class="form-control glass-input" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Progresso (%)</label>
                                <input type="range" id="editProgresso" class="form-range" min="0" max="100" value="0">
                                <div class="text-center">
                                    <span id="progressoValue" class="glass-text">0%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Categoria</label>
                                <select id="editCategoria" class="form-select glass-input">
                                    <option value="">Sem categoria</option>
                                    <option value="1">Trabalho</option>
                                    <option value="2">Estudos</option>
                                    <option value="3">Pessoal</option>
                                    <option value="4">Saúde</option>
                                    <option value="5">Lazer</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="edit-form-footer">
                    <div class="edit-shortcuts">
                        <small class="glass-text-muted">
                            <kbd>Ctrl+S</kbd> Salvar | <kbd>Esc</kbd> Cancelar
                        </small>
                    </div>
                    <div class="edit-actions">
                        <button class="btn btn-glass-secondary" onclick="closeEditModal()">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button class="btn btn-glass" onclick="saveEdit()">
                            <i class="fas fa-save me-2"></i>Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay de Remoção -->
<div id="deleteOverlay" class="delete-overlay">
    <div class="delete-overlay-background"></div>
    <div class="delete-container">
        <div class="delete-header">
            <h4><i class="fas fa-trash me-2"></i>Remover Atividade</h4>
            <p>Arraste a atividade para a lixeira vermelha ou clique na lixeira para confirmar a remoção</p>
        </div>
        <div class="delete-main">
            <div class="delete-item">
                <div id="deleteActivityCard" class="delete-activity-card">
                    <!-- Card da atividade será clonado aqui -->
                </div>
                <div class="delete-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="delete-target">
                    <div id="deleteTrash" class="delete-trash">
                        <i class="fas fa-trash"></i>
                        <span>Remover</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="delete-actions">
            <button class="btn btn-glass-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Overlay de Criação -->
<div id="createOverlay" class="edit-overlay">
    <div class="edit-overlay-background"></div>
    <div class="edit-container">
        <div class="edit-form-container">
            <div class="edit-form-card">
                <div class="edit-form-header">
                    <h5><i class="fas fa-plus me-2"></i>Nova Atividade</h5>
                    <button class="edit-close-btn" onclick="closeCreateModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="edit-form-body">
                    <form id="createForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label glass-text">Título</label>
                                <input type="text" name="titulo" class="form-control glass-input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Status</label>
                                <select name="status" class="form-select glass-input">
                                    <option value="pendente">Pendente</option>
                                    <option value="em_andamento">Em Andamento</option>
                                    <option value="concluida">Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Prioridade</label>
                                <select name="prioridade" class="form-select glass-input">
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Data de Início</label>
                                <input type="date" name="data_inicio" class="form-control glass-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Data de Conclusão</label>
                                <input type="date" name="data_fim" class="form-control glass-input">
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Descrição</label>
                                <textarea name="descricao" class="form-control glass-input" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Progresso (%)</label>
                                <input type="range" name="progresso" class="form-range" min="0" max="100" value="0">
                                <div class="text-center">
                                    <span id="createProgressoValue" class="glass-text">0%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Categoria</label>
                                <select name="categoria_id" class="form-select glass-input">
                                    <option value="">Sem categoria</option>
                                    <option value="1">Trabalho</option>
                                    <option value="2">Estudos</option>
                                    <option value="3">Pessoal</option>
                                    <option value="4">Saúde</option>
                                    <option value="5">Lazer</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="edit-form-footer">
                    <div class="edit-shortcuts">
                        <small class="glass-text-muted">
                            <kbd>Ctrl+S</kbd> Criar | <kbd>Esc</kbd> Cancelar
                        </small>
                    </div>
                    <div class="edit-actions">
                        <button class="btn btn-glass-secondary" onclick="closeCreateModal()">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button class="btn btn-glass" onclick="createAtividade()">
                            <i class="fas fa-plus me-2"></i>Criar Atividade
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para o overlay de criação */
#createOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

#createOverlay.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

#createOverlay .edit-form-card {
    animation: editFormEnter 0.4s ease-out forwards;
}

/* Animação de entrada para o formulário de criação */
@keyframes createFormEnter {
    0% {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

#createOverlay.show .edit-form-card {
    animation: createFormEnter 0.4s ease-out forwards;
}
</style>

<script>
// Inicialização segura das variáveis
let atividades = [];
let atividadesHistorico = [];
let currentView = 'cards';
let editingActivity = null;
let deletingActivity = null;

// Tentar carregar atividades do PHP, se disponível
try {
    @if(isset($atividades))
        atividades = @json($atividades);
    @endif
} catch (e) {
    console.log('Atividades não disponíveis via PHP, usando array vazio');
    atividades = [];
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Testar conectividade do servidor
    testServerConnection();
    
    // Carregar atividades ativas e concluídas do banco de dados
    loadAtividadesFromDatabase();
    
    // Só renderizar se as atividades foram carregadas com sucesso
    if (atividades && atividades.length >= 0) {
        renderAtividades(atividades);
        renderHistorico(atividadesHistorico);
        updateStats();
    } else {
        // Se não há atividades, mostrar estado vazio
        const container = document.getElementById('atividadesContainer');
        if (container) {
            container.innerHTML = `
                <div class="glass-card text-center p-5">
                    <div class="empty-state">
                        <i class="fas fa-tasks fa-3x glass-text-muted mb-3"></i>
                        <h4 class="glass-text">Nenhuma atividade encontrada</h4>
                        <p class="glass-text-muted">Crie sua primeira atividade para começar!</p>
                        <button class="btn btn-glass mt-3" onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i>Criar Atividade
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    // Event listeners para atalhos de teclado
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's' && editingActivity) {
            e.preventDefault();
            saveEdit();
        }
        if (e.key === 'Escape') {
            closeEditModal();
            closeDeleteModal();
        }
    });
    
    // Drag and drop para remoção
    setupDragAndDrop();
});

// Renderizar atividades
function renderAtividades(atividades) {
    const container = document.getElementById('atividadesContainer');
    
    if (atividades.length === 0) {
        container.innerHTML = `
            <div class="glass-card text-center p-5">
                <div class="empty-state">
                    <i class="fas fa-tasks fa-3x glass-text-muted mb-3"></i>
                    <h4 class="glass-text">Nenhuma atividade encontrada</h4>
                    <p class="glass-text-muted">Crie sua primeira atividade para começar!</p>
                    <button class="btn btn-glass mt-3" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Criar Atividade
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    if (currentView === 'cards') {
        renderCardsView(atividades);
    } else if (currentView === 'list') {
        renderListView(atividades);
    } else {
        renderCompactView(atividades);
    }
}

// Renderizar vista de cards
function renderCardsView(atividades) {
    const container = document.getElementById('atividadesContainer');
    container.innerHTML = `
        <div class="row">
            ${atividades.map(atividade => `
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="activity-card" data-id="${atividade.id}">
                        <div class="activity-content">
                            <div class="activity-info">
                                <div class="activity-header">
                                    <h6 class="activity-title">${atividade.titulo}</h6>
                                    <div class="activity-badges">
                                        <span class="glass-badge status-${atividade.status}">${getStatusText(atividade.status)}</span>
                                        <span class="glass-badge priority-${atividade.prioridade}">${getPriorityText(atividade.prioridade)}</span>
                                    </div>
                                </div>
                                <div class="activity-description">
                                    <p>${atividade.descricao || 'Sem descrição'}</p>
                                </div>
                                <div class="activity-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>${formatDate(atividade.created_at)}</span>
                                    </div>
                                    ${atividade.data_fim ? `
                                        <div class="meta-item">
                                            <i class="fas fa-flag-checkered"></i>
                                            <span>${formatDate(atividade.data_fim)}</span>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="activity-progress">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" style="width: ${atividade.progresso || 0}%"></div>
                                        </div>
                                        <small class="glass-text-muted me-2">${atividade.progresso || 0}%</small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-warning" onclick="updateStatus(${atividade.id}, 'pendente')" title="Marcar como Pendente" ${atividade.status === 'pendente' ? 'disabled' : ''}>
                                                <i class="fas fa-clock"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="updateStatus(${atividade.id}, 'em_andamento')" title="Marcar como Em Andamento" ${atividade.status === 'em_andamento' ? 'disabled' : ''}>
                                                <i class="fas fa-spinner"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="updateStatus(${atividade.id}, 'concluida')" title="Marcar como Concluída" ${atividade.status === 'concluida' ? 'disabled' : ''}>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Slider de progresso interativo -->
                                    <div class="mt-2">
                                        <input type="range" class="form-range" min="0" max="100" value="${atividade.progresso || 0}" 
                                               oninput="updateProgressFromSlider(${atividade.id}, this.value)" 
                                               onchange="updateProgressFromSlider(${atividade.id}, this.value)"
                                               style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 3px;">
                                    </div>
                                </div>
                            </div>
                            <div class="activity-actions">
                                <button class="btn btn-glass-secondary btn-sm" onclick="editActivity(${atividade.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${atividade.status === 'concluida' ? `
                                    <button class="btn btn-glass-success btn-sm" onclick="moveToHistory(${atividade.id})" title="Mover para Histórico">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-glass-danger btn-sm" onclick="deleteActivity(${atividade.id})" title="Remover">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Renderizar vista de lista
function renderListView(atividades) {
    const container = document.getElementById('atividadesContainer');
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Progresso</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    ${atividades.map(atividade => `
                        <tr>
                            <td>
                                <strong>${atividade.titulo}</strong>
                                <br><small class="glass-text-muted">${atividade.descricao || 'Sem descrição'}</small>
                            </td>
                            <td><span class="glass-badge status-${atividade.status}">${getStatusText(atividade.status)}</span></td>
                            <td><span class="glass-badge priority-${atividade.prioridade}">${getPriorityText(atividade.prioridade)}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="min-width: 100px;">
                                        <div class="progress-bar" style="width: ${atividade.progresso || 0}%"></div>
                                    </div>
                                    <small class="me-2">${atividade.progresso || 0}%</small>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-warning" onclick="updateStatus(${atividade.id}, 'pendente')" title="Marcar como Pendente" ${atividade.status === 'pendente' ? 'disabled' : ''}>
                                            <i class="fas fa-clock"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="updateStatus(${atividade.id}, 'em_andamento')" title="Marcar como Em Andamento" ${atividade.status === 'em_andamento' ? 'disabled' : ''}>
                                            <i class="fas fa-spinner"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="updateStatus(${atividade.id}, 'concluida')" title="Marcar como Concluída" ${atividade.status === 'concluida' ? 'disabled' : ''}>
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>${formatDate(atividade.created_at)}</td>
                            <td>
                                <button class="btn btn-glass-secondary btn-sm" onclick="editActivity(${atividade.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${atividade.status === 'concluida' ? `
                                    <button class="btn btn-glass-success btn-sm" onclick="moveToHistory(${atividade.id})" title="Mover para Histórico">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-glass-danger btn-sm" onclick="deleteActivity(${atividade.id})" title="Remover">
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

// Renderizar vista compacta
function renderCompactView(atividades) {
    const container = document.getElementById('atividadesContainer');
    container.innerHTML = `
        <div class="row">
            ${atividades.map(atividade => `
                <div class="col-12 mb-3">
                    <div class="activity-card-compact" data-id="${atividade.id}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="activity-info">
                                <h6>${atividade.titulo}</h6>
                                <div class="activity-badges">
                                    <span class="glass-badge status-${atividade.status}">${getStatusText(atividade.status)}</span>
                                    <span class="glass-badge priority-${atividade.prioridade}">${getPriorityText(atividade.prioridade)}</span>
                                </div>
                            </div>
                            <div class="activity-actions">
                                <button class="btn btn-glass-secondary btn-sm" onclick="editActivity(${atividade.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${atividade.status === 'concluida' ? `
                                    <button class="btn btn-glass-success btn-sm" onclick="moveToHistory(${atividade.id})" title="Mover para Histórico">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-glass-danger btn-sm" onclick="deleteActivity(${atividade.id})" title="Remover">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="activity-progress">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" style="width: ${atividade.progresso || 0}%"></div>
                                </div>
                                <small class="glass-text-muted me-2">${atividade.progresso || 0}%</small>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-warning" onclick="updateStatus(${atividade.id}, 'pendente')" title="Marcar como Pendente" ${atividade.status === 'pendente' ? 'disabled' : ''}>
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="updateStatus(${atividade.id}, 'em_andamento')" title="Marcar como Em Andamento" ${atividade.status === 'em_andamento' ? 'disabled' : ''}>
                                        <i class="fas fa-spinner"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="updateStatus(${atividade.id}, 'concluida')" title="Marcar como Concluída" ${atividade.status === 'concluida' ? 'disabled' : ''}>
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Aplicar filtros
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    const periodFilter = document.getElementById('periodFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();

    let filtered = [...atividades];

    // Aplicar filtros de período
    if (periodFilter === 'today') {
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        filtered = filtered.filter(a => new Date(a.created_at) >= todayStart);
    } else if (periodFilter === 'week') {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        filtered = filtered.filter(a => new Date(a.created_at) >= startOfWeek);
    } else if (periodFilter === 'month') {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        filtered = filtered.filter(a => new Date(a.created_at) >= startOfMonth);
    } else if (periodFilter === 'overdue') {
        const today = new Date();
        filtered = filtered.filter(a => a.data_fim && new Date(a.data_fim) < today && a.status !== 'concluida');
    }

    // Aplicar filtros de status
    if (statusFilter) {
        filtered = filtered.filter(a => a.status === statusFilter);
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
            case 'created_at':
                return new Date(b.created_at) - new Date(a.created_at);
            case 'data_fim':
                if (!a.data_fim && !b.data_fim) return 0;
                if (!a.data_fim) return 1;
                if (!b.data_fim) return -1;
                return new Date(a.data_fim) - new Date(b.data_fim);
            case 'prioridade':
                const priorityOrder = { alta: 3, media: 2, baixa: 1 };
                return priorityOrder[b.prioridade] - priorityOrder[a.prioridade];
            case 'status':
                const statusOrder = { pendente: 1, em_andamento: 2, concluida: 3 };
                return statusOrder[a.status] - statusOrder[b.status];
            case 'progresso':
                return (b.progresso || 0) - (a.progresso || 0);
            case 'titulo':
                return a.titulo.localeCompare(b.titulo);
            default:
                return 0;
        }
    });

    renderAtividades(filtered);
    updateStats();
}

// Mudar vista
function changeView() {
    currentView = document.getElementById('viewFilter').value;
    applyFilters();
}

// Atualizar estatísticas
function updateStats() {
    const total = atividades.length;
    const pendentes = atividades.filter(a => a.status === 'pendente').length;
    const emAndamento = atividades.filter(a => a.status === 'em_andamento').length;
    const concluidas = atividades.filter(a => a.status === 'concluida').length;

    document.getElementById('totalAtividades').textContent = total;
    document.getElementById('pendentes').textContent = pendentes;
    document.getElementById('emAndamento').textContent = emAndamento;
    document.getElementById('concluidas').textContent = concluidas;
}

// Funções auxiliares
function getStatusText(status) {
    const statusMap = {
        'pendente': 'Pendente',
        'em_andamento': 'Em Andamento',
        'concluida': 'Concluída'
    };
    return statusMap[status] || status;
}

function getPriorityText(priority) {
    const priorityMap = {
        'alta': 'Alta',
        'media': 'Média',
        'baixa': 'Baixa'
    };
    return priorityMap[priority] || priority;
}

function formatDate(dateString) {
    if (!dateString) return '';
    
    // Criar data considerando o fuso horário local
    const date = new Date(dateString);
    
    // Verificar se a data é válida
    if (isNaN(date.getTime())) {
        console.warn('Data inválida:', dateString);
        return '';
    }
    
    // Formatar para dd/mm/yyyy
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

// Funções de edição
function editActivity(id) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    editingActivity = atividade;
    
    // Função para formatar data para input type="date"
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        console.log('Formatando data para input:', dateString);
        
        // Se a data já está no formato YYYY-MM-DD, retornar como está
        if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return dateString;
        }
        
        // Para datas que vêm do servidor, criar a data considerando o fuso horário local
        // Usar UTC para evitar problemas de fuso horário
        const date = new Date(dateString);
        
        // Verificar se a data é válida
        if (isNaN(date.getTime())) {
            console.warn('Data inválida para input:', dateString);
            return '';
        }
        
        // Formatar para YYYY-MM-DD
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        const formattedDate = `${year}-${month}-${day}`;
        console.log('Data formatada para input:', formattedDate);
        return formattedDate;
    }
    
    // Preencher formulário
    console.log('Preenchendo formulário com dados:', atividade);
    
    document.getElementById('editId').value = atividade.id;
    document.getElementById('editTitulo').value = atividade.titulo;
    document.getElementById('editStatus').value = atividade.status;
    document.getElementById('editPrioridade').value = atividade.prioridade;
    document.getElementById('editDataInicio').value = formatDateForInput(atividade.data_inicio);
    document.getElementById('editDataFim').value = formatDateForInput(atividade.data_fim);
    document.getElementById('editDescricao').value = atividade.descricao || '';
    document.getElementById('editProgresso').value = atividade.progresso || 0;
    document.getElementById('editCategoria').value = atividade.categoria_id || '';
    document.getElementById('progressoValue').textContent = (atividade.progresso || 0) + '%';
    
    console.log('Formulário preenchido. Valores:');
    console.log('Título:', document.getElementById('editTitulo').value);
    console.log('Data Início:', document.getElementById('editDataInicio').value);
    console.log('Data Fim:', document.getElementById('editDataFim').value);
    




    // Criar card completo para highlight com todas as informações
    const highlightCardHTML = `
        <div class="activity-preview">
            <h6>${atividade.titulo}</h6>
            <p>${atividade.descricao || 'Sem descrição'}</p>
            <div class="preview-badges">
                <span class="preview-badge status-${atividade.status}">${getStatusText(atividade.status)}</span>
                <span class="preview-badge priority-${atividade.prioridade}">${getPriorityText(atividade.prioridade)}</span>
            </div>
            <div class="preview-dates">
                ${atividade.data_inicio ? `
                    <div class="preview-date">
                        <i class="fas fa-play"></i>
                        <span>Início: ${formatDate(atividade.data_inicio)}</span>
                    </div>
                ` : ''}
                ${atividade.data_fim ? `
                    <div class="preview-date">
                        <i class="fas fa-flag-checkered"></i>
                        <span>Conclusão: ${formatDate(atividade.data_fim)}</span>
                    </div>
                ` : ''}
                <div class="preview-date">
                    <i class="fas fa-calendar"></i>
                    <span>Criado: ${formatDate(atividade.created_at)}</span>
                </div>
                ${atividade.progresso ? `
                    <div class="preview-date">
                        <i class="fas fa-chart-line"></i>
                        <span>Progresso: ${atividade.progresso}%</span>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('editHighlightCard').innerHTML = highlightCardHTML;

    // Mostrar overlay
    document.getElementById('editOverlay').classList.add('show');
    
    // Animar entrada
    setTimeout(() => {
        document.getElementById('editHighlightCard').classList.add('highlight-glow');
    }, 100);
}

function closeEditModal() {
    console.log('Fechando modal de edição');
    
    const editOverlay = document.getElementById('editOverlay');
    const editHighlightCard = document.getElementById('editHighlightCard');
    
    if (editOverlay) {
        editOverlay.classList.remove('show');
    }
    
    if (editHighlightCard) {
        editHighlightCard.classList.remove('highlight-glow');
    }
    
    editingActivity = null;
    
    // Limpar formulário
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.reset();
    }
    
    console.log('Modal de edição fechado');
}

function saveEdit() {
    const formData = new FormData(document.getElementById('editForm'));
    
    // Preparar dados para envio
    const updateData = {
        titulo: document.getElementById('editTitulo').value,
        status: document.getElementById('editStatus').value,
        prioridade: document.getElementById('editPrioridade').value,
        data_inicio: document.getElementById('editDataInicio').value,
        data_fim: document.getElementById('editDataFim').value,
        descricao: document.getElementById('editDescricao').value,
        progresso: document.getElementById('editProgresso').value,
        categoria_id: document.getElementById('editCategoria').value || null
    };
    
    console.log('Enviando dados para atualização:', updateData);
    
    fetch(`/update-activity/${editingActivity.id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(updateData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Atualizar atividade na lista
            const index = atividades.findIndex(a => a.id === editingActivity.id);
            if (index !== -1) {
                atividades[index] = { ...atividades[index], ...data.atividade };
            }
            
            // Fechar modal primeiro
            closeEditModal();
            
            // Atualizar interface
            applyFilters();
            
            // Mostrar notificação
            showNotification('Atividade atualizada com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao atualizar atividade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar atividade:', error);
        showNotification('Erro ao atualizar atividade', 'error');
    });
}

// Funções de remoção
function deleteActivity(id) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    deletingActivity = atividade;
    
    // Clonar card para highlight
    const originalCard = document.querySelector(`[data-id="${id}"]`);
    if (originalCard) {
        const clonedCard = originalCard.cloneNode(true);
        document.getElementById('deleteActivityCard').innerHTML = '';
        document.getElementById('deleteActivityCard').appendChild(clonedCard);
        
        // Configurar drag and drop após clonar o card
        setTimeout(() => {
            setupDragAndDrop();
        }, 100);
    }

    // Mostrar overlay
    document.getElementById('deleteOverlay').classList.add('show');
    
    // Adicionar pulso na seta após a animação de entrada
    setTimeout(() => {
        const arrow = document.querySelector('.delete-arrow');
        if (arrow) {
            arrow.classList.add('pulse');
        }
    }, 800);
    
    console.log('Overlay de remoção aberto para atividade:', atividade.titulo);
}

function closeDeleteModal() {
    document.getElementById('deleteOverlay').classList.remove('show');
    deletingActivity = null;
    
    // Limpar classes de destaque da lixeira
    const deleteTrash = document.getElementById('deleteTrash');
    if (deleteTrash) {
        deleteTrash.classList.remove('active', 'highlight');
    }
    
    // Remover pulso da seta
    const arrow = document.querySelector('.delete-arrow');
    if (arrow) {
        arrow.classList.remove('pulse');
    }
    
    console.log('Overlay de remoção fechado');
}

function setupDragAndDrop() {
    const deleteCard = document.getElementById('deleteActivityCard');
    const deleteTrash = document.getElementById('deleteTrash');
    
    if (deleteCard && deleteTrash) {
        // Tornar o card arrastável
        deleteCard.draggable = true;
        
        deleteCard.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', '');
            this.classList.add('dragging');
            console.log('Iniciando arrasto do card');
        });
        
        deleteCard.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            console.log('Finalizando arrasto do card');
        });
        
        deleteTrash.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('active');
            console.log('Arrasto sobre a lixeira');
        });
        
        deleteTrash.addEventListener('dragleave', function(e) {
            this.classList.remove('active');
            console.log('Saindo da lixeira');
        });
        
        deleteTrash.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('active');
            this.classList.add('highlight');
            
            console.log('Soltando na lixeira - confirmando remoção');
            
            // Confirmar remoção
            if (confirm('Tem certeza que deseja remover esta atividade?')) {
                removeActivity(deletingActivity.id);
            } else {
                this.classList.remove('highlight');
            }
        });
        
        // Adicionar evento de clique na lixeira como alternativa
        deleteTrash.addEventListener('click', function() {
            if (deletingActivity) {
                this.classList.add('highlight');
                if (confirm('Tem certeza que deseja remover esta atividade?')) {
                    removeActivity(deletingActivity.id);
                } else {
                    this.classList.remove('highlight');
                }
            }
        });
    }
}

function removeActivity(id) {
    console.log('Iniciando remoção da atividade:', id);
    
    // Adicionar efeito visual de remoção
    const deleteCard = document.getElementById('deleteActivityCard');
    if (deleteCard) {
        deleteCard.style.transform = 'scale(0.8) rotate(5deg)';
        deleteCard.style.opacity = '0.5';
        deleteCard.style.transition = 'all 0.3s ease';
    }
    
    fetch(`/delete-activity/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover da lista
            atividades = atividades.filter(a => a.id !== id);
            
            // Efeito de sucesso na lixeira
            const deleteTrash = document.getElementById('deleteTrash');
            if (deleteTrash) {
                deleteTrash.style.transform = 'scale(1.2)';
                deleteTrash.style.background = 'rgba(76, 175, 80, 0.3)';
                deleteTrash.style.borderColor = '#4caf50';
                setTimeout(() => {
                    deleteTrash.style.transform = 'scale(1)';
                    deleteTrash.style.background = '';
                    deleteTrash.style.borderColor = '';
                }, 500);
            }
            
            setTimeout(() => {
                closeDeleteModal();
                applyFilters();
                
                // Mostrar notificação
                showNotification('Atividade removida com sucesso!', 'success');
            }, 300);
        } else {
            console.error('Erro na resposta:', data.message);
            showNotification('Erro ao remover atividade: ' + data.message, 'error');
            
            // Restaurar card se houver erro
            if (deleteCard) {
                deleteCard.style.transform = '';
                deleteCard.style.opacity = '';
            }
        }
    })
    .catch(error => {
        console.error('Erro ao remover atividade:', error);
        showNotification('Erro ao remover atividade', 'error');
        
        // Restaurar card se houver erro
        if (deleteCard) {
            deleteCard.style.transform = '';
            deleteCard.style.opacity = '';
        }
    });
}

// Funções de criação
function openCreateModal() {
    // Mostrar overlay
    document.getElementById('createOverlay').classList.add('show');
    
    // Limpar formulário ao abrir
    document.getElementById('createForm').reset();
    
    // Resetar valor do progresso
    document.getElementById('createProgressoValue').textContent = '0%';
    
    // Focar no primeiro campo
    setTimeout(() => {
        const tituloInput = document.querySelector('#createForm input[name="titulo"]');
        if (tituloInput) {
            tituloInput.focus();
        }
    }, 300);
    
    // Adicionar evento para range do progresso
    const progressoRange = document.querySelector('#createForm input[name="progresso"]');
    if (progressoRange) {
        progressoRange.addEventListener('input', function() {
            document.getElementById('createProgressoValue').textContent = this.value + '%';
        });
    }
    
    // Adicionar eventos de teclado
    document.addEventListener('keydown', handleCreateKeyboard);
}

function closeCreateModal() {
    document.getElementById('createOverlay').classList.remove('show');
    document.removeEventListener('keydown', handleCreateKeyboard);
}

function handleCreateKeyboard(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        createAtividade();
    } else if (e.key === 'Escape') {
        closeCreateModal();
    }
}

function createAtividade() {
    const formData = new FormData(document.getElementById('createForm'));
    
    // Validar campos obrigatórios
    const titulo = formData.get('titulo');
    if (!titulo || titulo.trim() === '') {
        alert('Por favor, preencha o título da atividade');
        return;
    }
    
    // Verificar se o usuário está autenticado
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Erro de autenticação. Por favor, faça login novamente.');
        window.location.href = '/login';
        return;
    }
    
    fetch('/save-activity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            titulo: titulo,
            status: formData.get('status'),
            prioridade: formData.get('prioridade'),
            data_inicio: formData.get('data_inicio'),
            data_fim: formData.get('data_fim'),
            descricao: formData.get('descricao'),
            progresso: formData.get('progresso'),
            categoria_id: formData.get('categoria_id')
        })
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Rota não encontrada. Verifique se o servidor está rodando e as rotas estão configuradas.');
            } else if (response.status === 405) {
                throw new Error('Método não permitido. Verifique se a rota está configurada corretamente.');
            } else if (response.status === 401) {
                throw new Error('Não autorizado. Por favor, faça login novamente.');
            } else if (response.status === 403) {
                throw new Error('Acesso negado. Verifique suas permissões.');
            } else {
                throw new Error(`Erro HTTP ${response.status}: ${response.statusText}`);
            }
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Adicionar à lista
            atividades.push(data.atividade);
            
            // Fechar overlay
            closeCreateModal();
            
            // Limpar formulário
            document.getElementById('createForm').reset();
            
            // Atualizar lista
            renderAtividades(atividades);
            updateStats();
            
            // Mostrar notificação
            showNotification('Atividade criada com sucesso!', 'success');
        } else {
            throw new Error(data.message || 'Erro ao criar atividade');
        }
    })
    .catch(error => {
        console.error('Erro ao criar atividade:', error);
        showNotification('Erro ao criar atividade: ' + error.message, 'error');
    });
}

// Funções de atualização de status
function updateStatus(id, newStatus) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    // Mostrar loading
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    fetch(`/atividades/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
            .then(data => {
            if (data.success) {
                // Recarregar dados do banco de dados após atualização
                loadAtividadesFromDatabase();
                
                // Mostrar notificação
                if (newStatus === 'concluida') {
                    showNotification('Atividade concluída e movida para o histórico!', 'success');
                } else {
                    showNotification(`Status atualizado para ${getStatusText(newStatus)}!`, 'success');
                }
            } else {
            throw new Error(data.message || 'Erro ao atualizar status');
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar status:', error);
        showNotification('Erro ao atualizar status: ' + error.message, 'error');
        
        // Restaurar botão
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Função para atualizar progresso via barra de progresso
function updateProgressFromSlider(id, newProgress) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    // Atualizar progresso na atividade
    atividade.progresso = parseInt(newProgress);
    
    // Atualizar interface
    renderAtividades(atividades);
    
    // Se progresso chegou a 100%, sugerir conclusão
    if (newProgress == 100) {
        showNotification('Progresso 100%! Considere marcar como concluída.', 'info');
    }
}

// Função para renderizar histórico
function renderHistorico(atividadesHistorico) {
    const container = document.getElementById('historyContainer');
    if (!container) return;

    if (atividadesHistorico.length === 0) {
        container.innerHTML = `
            <div class="glass-card text-center p-5">
                <div class="empty-state">
                    <i class="fas fa-history fa-3x glass-text-muted mb-3"></i>
                    <h4 class="glass-text">Nenhuma atividade no histórico</h4>
                    <p class="glass-text-muted">As atividades concluídas aparecerão aqui</p>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="row">
            ${atividadesHistorico.map(atividade => `
                <div class="col-12 mb-3">
                    <div class="glass-card p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="glass-text mb-1">${atividade.titulo}</h6>
                                <p class="glass-text-muted small mb-2">${atividade.descricao || 'Sem descrição'}</p>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="glass-badge status-${atividade.status}">${getStatusText(atividade.status)}</span>
                                    <span class="glass-badge priority-${atividade.prioridade}">${getPriorityText(atividade.prioridade)}</span>
                                    <small class="glass-text-muted">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        Concluída em: ${formatDate(atividade.completed_at)}
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="progress mb-2" style="width: 100px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <small class="glass-text-muted">100% concluído</small>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Função para carregar atividades do banco de dados
async function loadAtividadesFromDatabase() {
    try {
        // Carregar atividades ativas
        const responseAtivas = await fetch('/api/atividades/ativas');
        if (responseAtivas.ok) {
            const dataAtivas = await responseAtivas.json();
            if (dataAtivas.success) {
                atividades = dataAtivas.data;
            }
        }

        // Carregar atividades concluídas (histórico)
        const responseConcluidas = await fetch('/api/atividades/concluidas');
        if (responseConcluidas.ok) {
            const dataConcluidas = await responseConcluidas.json();
            if (dataConcluidas.success) {
                atividadesHistorico = dataConcluidas.data;
            }
        }

        // Renderizar as atividades carregadas
        renderAtividades(atividades);
        renderHistorico(atividadesHistorico);
        updateStats();

    } catch (error) {
        console.error('Erro ao carregar atividades do banco de dados:', error);
        showNotification('Erro ao carregar atividades', 'error');
    }
}

// Função para aplicar filtros do histórico
function applyHistoryFilters() {
    const statusFilter = document.getElementById('historyStatusFilter').value;
    const periodFilter = document.getElementById('historyPeriodFilter').value;
    const sortFilter = document.getElementById('historySortFilter').value;
    const searchInput = document.getElementById('historySearchInput').value.toLowerCase();

    let filtered = [...atividadesHistorico];

    // Aplicar filtros de período
    if (periodFilter === 'today') {
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        filtered = filtered.filter(a => new Date(a.completed_at) >= todayStart);
    } else if (periodFilter === 'week') {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        filtered = filtered.filter(a => new Date(a.completed_at) >= startOfWeek);
    } else if (periodFilter === 'month') {
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        filtered = filtered.filter(a => new Date(a.completed_at) >= startOfMonth);
    } else if (periodFilter === 'year') {
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        filtered = filtered.filter(a => new Date(a.completed_at) >= startOfYear);
    }

    // Aplicar filtros de status
    if (statusFilter) {
        filtered = filtered.filter(a => a.status === statusFilter);
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

// Teste de conectividade do servidor
function testServerConnection() {
    fetch('/test', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            console.warn('Servidor pode não estar funcionando corretamente');
        }
    })
    .catch(error => {
        console.warn('Erro de conectividade com o servidor:', error);
        showNotification('Aviso: Servidor pode não estar funcionando corretamente', 'info');
    });
}

// Notificações
function showNotification(message, type) {
    // Criar elemento de notificação
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
    
    // Adicionar ao body
    document.body.appendChild(notification);
    
    // Remover após 3 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Função para mover atividade para o histórico
function moveToHistory(id) {
    const atividade = atividades.find(a => a.id === id);
    if (!atividade) return;

    // Confirmar a ação
    if (!confirm(`Deseja mover "${atividade.titulo}" para o histórico?`)) {
        return;
    }

    // Marcar como arquivada (mover para histórico)
    fetch(`/archive-activity/${id}`, {
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
            // Remover da lista de atividades ativas
            atividades = atividades.filter(a => a.id !== id);
            
            // Atualizar interface
            applyFilters();
            
            // Mostrar notificação
            showNotification('Atividade movida para o histórico com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao mover para histórico', 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao mover para histórico:', error);
        showNotification('Erro ao mover para histórico', 'error');
    });
}

// Event listeners
document.getElementById('editProgresso').addEventListener('input', function() {
    document.getElementById('progressoValue').textContent = this.value + '%';
});
</script>
@endsection 
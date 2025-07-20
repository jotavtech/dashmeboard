<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atividades - DashMEBoard Neon</title>
    
    <!-- Favicon Neon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/glassmorphism.css') }}" rel="stylesheet">
</head>
<body class="dashboard-background">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg glass-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-tasks me-2 glass-icon"></i>DashMEBoard
            </a>
            
            <div class="d-flex align-items-center ms-auto">
                <!-- Barrinha única - Menu dropdown -->
                <div class="animated-bars">
                    <div class="animated-bar" onclick="toggleDropdownMenu()">
                        <div class="bar-line"></div>
                        <div class="bar-line"></div>
                    </div>
                </div>
                
                <!-- Menu do usuário -->
                <div class="navbar-nav ms-3">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1 glass-icon"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user-edit me-2"></i>Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Fullscreen Menu -->
    <div class="dropdown-menu-overlay" id="dropdownMenuOverlay">
        <div class="dropdown-menu-content">
            <div class="fullscreen-close-btn" onclick="closeDropdownMenu()"></div>
            <div class="fullscreen-menu-layout">
                <!-- Seção do Relógio Gigante -->
                <div class="fullscreen-clock-section">
                    <div class="giant-clock-label">Hora Atual</div>
                    <div class="fullscreen-giant-clock" id="dropdownClock"></div>
                    <div class="giant-clock-date" id="dropdownDate"></div>
                </div>
                
                <!-- Seção dos Links de Navegação -->
                <div class="fullscreen-navigation-section">
                    <h2 class="navigation-title">Navegação</h2>
                    <div class="navigation-links">
                        <a href="{{ route('dashboard') }}" class="nav-link-item">
                            <i class="fas fa-home" style="color: #06b6d4;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Dashboard</div>
                                <div class="nav-link-description">Voltar ao painel principal</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('atividades') }}" class="nav-link-item">
                            <i class="fas fa-tasks" style="color: #8b5cf6;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Atividades</div>
                                <div class="nav-link-description">Página atual</div>
                            </div>
                        </a>
                        
                        <button class="nav-link-item" onclick="openNovaAtividadeModal()">
                            <i class="fas fa-plus" style="color: #10b981;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Nova Atividade</div>
                                <div class="nav-link-description">Criar nova tarefa</div>
                            </div>
                        </button>
                        
                        <a href="{{ route('dashboard') }}#projetos" class="nav-link-item">
                            <i class="fas fa-folder" style="color: #f59e0b;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Projetos</div>
                                <div class="nav-link-description">Gerenciar projetos</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('profile') }}" class="nav-link-item">
                            <i class="fas fa-user" style="color: #ef4444;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Meu Perfil</div>
                                <div class="nav-link-description">Configurações e estatísticas</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container my-4">
        <div class="main-content-card">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title mb-4">
                        <div class="section-icon">📋</div>
                        <h2 class="glass-text mb-0">Minhas Atividades</h2>
                        <p class="glass-text-muted">Interface simples</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-filter me-2" style="color: #f59e0b; opacity: 0.8;"></i>
                            Filtros
                        </h5>
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label glass-text small">Status</label>
                                <select class="form-select glass-input" id="filtroStatus">
                                    <option value="">Todos os Status</option>
                                    <option value="pendente">🔄 Pendente</option>
                                    <option value="em_andamento">⚡ Em Andamento</option>
                                    <option value="concluida">✅ Concluída</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label glass-text small">Prioridade</label>
                                <select class="form-select glass-input" id="filtroPrioridade">
                                    <option value="">Todas as Prioridades</option>
                                    <option value="alta">🔥 Alta</option>
                                    <option value="media">🔸 Média</option>
                                    <option value="baixa">🔹 Baixa</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-8">
                                <label class="form-label glass-text small">Buscar</label>
                                <input type="text" class="form-control glass-input" id="buscar" placeholder="🔍 Buscar atividades...">
                            </div>
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button class="btn glass-button w-100" id="limparFiltros">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão Nova Atividade -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <button class="btn glass-button btn-lg" data-bs-toggle="modal" data-bs-target="#novaAtividadeModal">
                        <i class="fas fa-plus me-2"></i>✨ Nova Atividade
                    </button>
                </div>
            </div>

            <!-- Lista de Atividades -->
            <div class="row">
                <div class="col-12">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-4">
                            <i class="fas fa-list me-2" style="color: #06b6d4; opacity: 0.8;"></i>
                            Lista de Atividades
                        </h5>
                        <div id="listaAtividades">
                            <div class="text-center py-5">
                                <div class="spinner-border glass-spinner" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="glass-text-muted mt-3">Carregando atividades...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Atividade -->
    <div class="modal fade" id="novaAtividadeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-modal">
                <div class="modal-header glass-modal-header">
                    <h5 class="modal-title glass-text">
                        <i class="fas fa-plus me-2" style="color: #00ffff;"></i>✨ Nova Atividade
                    </h5>
                    <button type="button" class="btn-close glass-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNovaAtividade">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label glass-text">Título *</label>
                                <input type="text" class="form-control glass-input" name="titulo" required placeholder="Digite o título da atividade...">
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Descrição</label>
                                <textarea class="form-control glass-input" name="descricao" rows="4" placeholder="Descreva sua atividade..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Status</label>
                                <select class="form-select glass-input" name="status">
                                    <option value="pendente">🔄 Pendente</option>
                                    <option value="em_andamento">⚡ Em Andamento</option>
                                    <option value="concluida">✅ Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Prioridade</label>
                                <select class="form-select glass-input" name="prioridade">
                                    <option value="baixa">🔹 Baixa</option>
                                    <option value="media" selected>🔸 Média</option>
                                    <option value="alta">🔥 Alta</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Data Limite</label>
                                <input type="date" class="form-control glass-input" name="data_limite">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer glass-modal-footer">
                    <button type="button" class="btn glass-button-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn glass-button" id="salvarAtividade">
                        <i class="fas fa-save me-2"></i>Salvar Atividade
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay de Edição Animado -->
    <div class="edit-overlay" id="editOverlay">
        <div class="edit-overlay-background" onclick="closeEditOverlay()"></div>
        <div class="edit-container">
            <!-- Card da Atividade Destacada -->
            <div class="edit-highlight-card" id="editHighlightCard">
                <div class="highlight-glow"></div>
                <div class="activity-preview" id="activityPreview">
                    <!-- Conteúdo da atividade será inserido aqui -->
                </div>
            </div>
            
            <!-- Formulário de Edição -->
            <div class="edit-form-container" id="editFormContainer">
                <div class="edit-form-card">
                    <div class="edit-form-header">
                        <h5 class="glass-text">
                            <i class="fas fa-edit me-2" style="color: #f59e0b;"></i>Editar Atividade
                        </h5>
                        <button type="button" class="edit-close-btn" onclick="closeEditOverlay()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="formEditAtividade" class="edit-form-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label glass-text">Título *</label>
                                <input type="text" class="form-control glass-input" id="editTitulo" name="titulo" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Descrição</label>
                                <textarea class="form-control glass-input" id="editDescricao" name="descricao" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Status</label>
                                <select class="form-select glass-input" id="editStatus" name="status">
                                    <option value="pendente">🔄 Pendente</option>
                                    <option value="em_andamento">⚡ Em Andamento</option>
                                    <option value="concluida">✅ Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label glass-text">Prioridade</label>
                                <select class="form-select glass-input" id="editPrioridade" name="prioridade">
                                    <option value="baixa">🔹 Baixa</option>
                                    <option value="media">🔸 Média</option>
                                    <option value="alta">🔥 Alta</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label glass-text">Data Limite</label>
                                <input type="date" class="form-control glass-input" id="editDataLimite" name="data_limite">
                            </div>
                        </div>
                    </form>
                    
                    <div class="edit-form-footer">
                        <div class="edit-shortcuts">
                            <small class="glass-text-muted">
                                <i class="fas fa-keyboard me-1"></i>
                                <kbd>Ctrl+S</kbd> Salvar • <kbd>Esc</kbd> Cancelar
                            </small>
                        </div>
                        <div class="edit-actions">
                            <button type="button" class="btn glass-button-secondary" onclick="closeEditOverlay()">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="button" class="btn glass-button" id="salvarEdicao">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Função para atualizar o relógio em tempo real
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            
            // Atualizar relógio gigante do fullscreen
            const dropdownClockElement = document.getElementById('dropdownClock');
            if (dropdownClockElement) {
                dropdownClockElement.textContent = timeString;
            }
            
            // Atualizar data
            const dropdownDateElement = document.getElementById('dropdownDate');
            if (dropdownDateElement) {
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                };
                const dateString = now.toLocaleDateString('pt-BR', options);
                dropdownDateElement.textContent = dateString;
            }
        }
        
        // Atualizar o relógio a cada segundo
        setInterval(updateClock, 1000);
        updateClock(); // Chamar imediatamente ao carregar a página
        
        // Estado do dropdown menu
        let dropdownVisible = false;
        
        // Função para alternar o dropdown menu
        function toggleDropdownMenu() {
            const dropdownOverlay = document.getElementById('dropdownMenuOverlay');
            
            if (dropdownVisible) {
                closeDropdownMenu();
            } else {
                openDropdownMenu();
            }
        }
        
        // Função para abrir o dropdown menu
        function openDropdownMenu() {
            const dropdownOverlay = document.getElementById('dropdownMenuOverlay');
            if (dropdownOverlay) {
                dropdownOverlay.classList.add('show');
                dropdownVisible = true;
            }
        }
        
        // Função para fechar o dropdown menu
        function closeDropdownMenu() {
            const dropdownOverlay = document.getElementById('dropdownMenuOverlay');
            if (dropdownOverlay) {
                dropdownOverlay.classList.remove('show');
                dropdownVisible = false;
            }
        }
        
        // Função para abrir modal de nova atividade
        function openNovaAtividadeModal() {
            closeDropdownMenu();
            const modal = new bootstrap.Modal(document.getElementById('novaAtividadeModal'));
            modal.show();
        }
        
        // Função para atualizar página
        function refreshPage() {
            closeDropdownMenu();
            location.reload();
        }
        
        // Fechar dropdown ao pressionar ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDropdownMenu();
            }
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const dropdownOverlay = document.getElementById('dropdownMenuOverlay');
            const animatedBar = document.querySelector('.animated-bar');
            
            if (dropdownVisible && 
                !dropdownOverlay.contains(event.target) && 
                !animatedBar.contains(event.target)) {
                closeDropdownMenu();
            }
        });

        // Carregar atividades
        async function carregarAtividades() {
            try {
                const response = await fetch('/api/atividades');
                const data = await response.json();
                
                if (data.success) {
                    // Armazenar atividades globalmente
                    atividades = data.data;
                    atividadesOriginais = [...data.data]; // Para os filtros
                    console.log('Atividades carregadas:', atividades.length);
                    
                    // Configurar event listeners dos filtros na primeira carga
                    if (!window.filtrosConfigurados) {
                        configurarEventosFiltros();
                        window.filtrosConfigurados = true;
                        console.log('Event listeners dos filtros configurados');
                    }
                    
                    renderizarAtividades(data.data);
                } else {
                    console.error('Erro ao carregar atividades:', data);
                }
            } catch (error) {
                console.error('Erro ao carregar atividades:', error);
                const container = document.getElementById('listaAtividades');
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x glass-icon mb-3" style="color: #f59e0b; opacity: 0.6;"></i>
                        <h5 class="glass-text">Erro ao carregar atividades</h5>
                        <p class="glass-text-muted">Tente atualizar a página</p>
                        <button class="btn glass-button" onclick="carregarAtividades()">
                            <i class="fas fa-sync me-2"></i>Tentar Novamente
                        </button>
                    </div>
                `;
            }
        }

        // Renderizar lista de atividades
        function renderizarAtividades(atividades) {
            const container = document.getElementById('listaAtividades');
            
            if (atividades.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x glass-icon mb-3" style="color: #06b6d4; opacity: 0.6;"></i>
                        <h5 class="glass-text">Nenhuma atividade encontrada</h5>
                        <p class="glass-text-muted">Clique em "Nova Atividade" para começar</p>
                        <button class="btn glass-button" data-bs-toggle="modal" data-bs-target="#novaAtividadeModal">
                            <i class="fas fa-plus me-2"></i>Criar Primeira Atividade
                        </button>
                    </div>
                `;
                return;
            }

            const html = atividades.map(atividade => {
                const statusConfig = {
                    'concluida': { color: '#10b981', icon: 'check-circle', label: 'Concluída' },
                    'em_andamento': { color: '#f59e0b', icon: 'clock', label: 'Em Andamento' },
                    'pendente': { color: '#6b7280', icon: 'circle', label: 'Pendente' }
                };
                
                const prioridadeConfig = {
                    'alta': { color: '#ef4444', icon: 'arrow-up', label: 'Alta' },
                    'media': { color: '#f59e0b', icon: 'minus', label: 'Média' },
                    'baixa': { color: '#06b6d4', icon: 'arrow-down', label: 'Baixa' }
                };
                
                const status = statusConfig[atividade.status] || statusConfig['pendente'];
                const prioridade = prioridadeConfig[atividade.prioridade] || prioridadeConfig['media'];
                
                return `
                    <div class="activity-card mb-3 p-4" data-activity-id="${atividade.id}" style="border-left: 4px solid ${prioridade.color};">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="glass-text mb-2">${atividade.titulo}</h6>
                                <p class="glass-text-muted mb-2">${atividade.descricao || 'Sem descrição'}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="glass-badge" style="background: ${status.color}20; color: ${status.color};">
                                        <i class="fas fa-${status.icon} me-1"></i>${status.label}
                                    </span>
                                    <span class="glass-badge" style="background: ${prioridade.color}20; color: ${prioridade.color};">
                                        <i class="fas fa-${prioridade.icon} me-1"></i>${prioridade.label}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="mb-2">
                                    <small class="glass-text-muted">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        ${new Date(atividade.created_at).toLocaleDateString('pt-BR')}
                                    </small>
                                </div>
                                ${atividade.data_limite ? 
                                    `<div class="mb-2">
                                        <small class="glass-text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            ${new Date(atividade.data_limite).toLocaleDateString('pt-BR')}
                                        </small>
                                    </div>` : ''}
                                <div class="btn-group">
                                    <button class="btn btn-sm glass-button-sm edit-activity-btn" onclick="editarAtividade(${atividade.id})" title="Editar atividade (Ctrl+E)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm glass-button-danger" onclick="excluirAtividade(${atividade.id})" title="Excluir atividade">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = html;
            
            // Adicionar efeitos visuais aos cards
            addActivityCardEffects();
        }

        // Salvar nova atividade
        document.getElementById('salvarAtividade').addEventListener('click', async () => {
            const form = document.getElementById('formNovaAtividade');
            const formData = new FormData(form);
            const button = document.getElementById('salvarAtividade');
            
            // Desabilitar botão durante salvamento
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
            
            try {
                const response = await fetch('/api/atividades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const data = await response.json();
                
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('novaAtividadeModal')).hide();
                    form.reset();
                    carregarAtividades();
                    
                    // Mostrar notificação de sucesso
                    showNotification('✅ Atividade criada com sucesso!', 'success');
                } else {
                    console.error('Erro ao salvar atividade:', data);
                    showNotification('❌ Erro ao criar atividade', 'error');
                }
            } catch (error) {
                console.error('Erro ao salvar atividade:', error);
                showNotification('❌ Erro de conexão', 'error');
            } finally {
                // Reabilitar botão
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Atividade';
            }
        });

        // Função para mostrar notificações
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `glass-notification ${type}`;
            notification.innerHTML = `
                <div class="glass-card p-3" style="position: fixed; top: 100px; right: 20px; z-index: 9999; min-width: 300px;">
                    <p class="glass-text mb-0">${message}</p>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remover após 3 segundos
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Variáveis globais para edição
        let currentEditingId = null;
        let currentAtividadeData = null;

        // Função para editar atividade
        async function editarAtividade(id) {
            try {
                // Destacar atividade sendo editada
                highlightEditingActivity(id);
                
                // Buscar dados da atividade
                const response = await fetch(`/api/atividades/${id}`);
                const data = await response.json();
                
                if (data.success) {
                    currentEditingId = id;
                    currentAtividadeData = data.data;
                    openEditOverlay(data.data);
                } else {
                    showNotification('❌ Erro ao carregar atividade', 'error');
                }
            } catch (error) {
                console.error('Erro ao buscar atividade:', error);
                showNotification('❌ Erro de conexão', 'error');
            }
        }

        // Abrir overlay de edição com animação
        function openEditOverlay(atividade) {
            const overlay = document.getElementById('editOverlay');
            
            // Popular preview da atividade
            populateActivityPreview(atividade);
            
            // Popular formulário de edição
            populateEditForm(atividade);
            
            // Mostrar overlay com animação
            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.classList.add('show');
            }, 10);
            
            // Bloquear scroll da página
            document.body.style.overflow = 'hidden';
        }

        // Popular preview da atividade
        function populateActivityPreview(atividade) {
            const preview = document.getElementById('activityPreview');
            
            const prioridades = {
                'baixa': { label: 'Baixa', color: '#06b6d4', icon: 'arrow-down' },
                'media': { label: 'Média', color: '#f59e0b', icon: 'minus' },
                'alta': { label: 'Alta', color: '#ef4444', icon: 'arrow-up' }
            };
            
            const status = {
                'pendente': { label: 'Pendente', color: '#6b7280', icon: 'clock' },
                'em_andamento': { label: 'Em Andamento', color: '#f59e0b', icon: 'play' },
                'concluida': { label: 'Concluída', color: '#10b981', icon: 'check' }
            };
            
            const prioridade = prioridades[atividade.prioridade] || prioridades['media'];
            const statusInfo = status[atividade.status] || status['pendente'];
            
            preview.innerHTML = `
                <h6>${atividade.titulo}</h6>
                <p>${atividade.descricao || 'Sem descrição'}</p>
                
                <div class="preview-badges">
                    <span class="preview-badge" style="background: ${statusInfo.color}20; color: ${statusInfo.color};">
                        <i class="fas fa-${statusInfo.icon} me-1"></i>${statusInfo.label}
                    </span>
                    <span class="preview-badge" style="background: ${prioridade.color}20; color: ${prioridade.color};">
                        <i class="fas fa-${prioridade.icon} me-1"></i>${prioridade.label}
                    </span>
                </div>
                
                <div class="preview-dates">
                    <div class="preview-date">
                        <i class="fas fa-calendar-plus"></i>
                        Criado: ${new Date(atividade.created_at).toLocaleDateString('pt-BR')}
                    </div>
                    ${atividade.data_limite ? `
                        <div class="preview-date">
                            <i class="fas fa-calendar-alt"></i>
                            Prazo: ${new Date(atividade.data_limite).toLocaleDateString('pt-BR')}
                        </div>
                    ` : ''}
                </div>
            `;
        }

        // Popular formulário de edição
        function populateEditForm(atividade) {
            document.getElementById('editTitulo').value = atividade.titulo || '';
            document.getElementById('editDescricao').value = atividade.descricao || '';
            document.getElementById('editStatus').value = atividade.status || 'pendente';
            document.getElementById('editPrioridade').value = atividade.prioridade || 'media';
            
            // Formatar data para input type="date"
            if (atividade.data_limite) {
                const date = new Date(atividade.data_limite);
                const formattedDate = date.toISOString().split('T')[0];
                document.getElementById('editDataLimite').value = formattedDate;
            } else {
                document.getElementById('editDataLimite').value = '';
            }
        }

        // Fechar overlay de edição
        function closeEditOverlay() {
            const overlay = document.getElementById('editOverlay');
            
            // Animação de saída
            overlay.classList.add('hiding');
            overlay.classList.remove('show');
            
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('hiding');
                
                // Restaurar scroll da página
                document.body.style.overflow = 'auto';
                
                // Limpar dados
                currentEditingId = null;
                currentAtividadeData = null;
                
                // Remover destaque da atividade
                const editingCard = document.querySelector('.activity-card.editing');
                if (editingCard) {
                    editingCard.classList.remove('editing');
                }
            }, 400);
        }

        // Salvar edição
        document.getElementById('salvarEdicao').addEventListener('click', async () => {
            if (!currentEditingId) return;
            
            // Validar formulário
            if (!validateEditForm()) return;
            
            const form = document.getElementById('formEditAtividade');
            const formData = new FormData(form);
            const button = document.getElementById('salvarEdicao');
            
            // Desabilitar botão durante salvamento
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
            
            try {
                const response = await fetch(`/api/atividades/${currentEditingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mostrar notificação de sucesso antes de fechar
                    showNotification('✅ Atividade atualizada com sucesso!', 'success');
                    
                    // Fechar overlay com delay para mostrar feedback
                    setTimeout(() => {
                        closeEditOverlay();
                        carregarAtividades(); // Recarregar lista
                    }, 500);
                } else {
                    console.error('Erro ao atualizar atividade:', data);
                    showNotification('❌ Erro ao atualizar atividade', 'error');
                }
            } catch (error) {
                console.error('Erro ao salvar edição:', error);
                showNotification('❌ Erro de conexão', 'error');
            } finally {
                // Reabilitar botão
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Alterações';
            }
        });

        // Atalhos de teclado
        document.addEventListener('keydown', function(event) {
            // ESC - Fechar overlay de edição
            if (event.key === 'Escape' && document.getElementById('editOverlay').classList.contains('show')) {
                closeEditOverlay();
                return;
            }
            
            // Ctrl+S - Salvar edição
            if (event.ctrlKey && event.key === 's' && document.getElementById('editOverlay').classList.contains('show')) {
                event.preventDefault();
                document.getElementById('salvarEdicao').click();
                return;
            }
            
            // Ctrl+N - Nova atividade
            if (event.ctrlKey && event.key === 'n' && !document.getElementById('editOverlay').classList.contains('show')) {
                event.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('novaAtividadeModal'));
                modal.show();
                return;
            }
        });
        
        // Adicionar efeitos visuais aos cards de atividade
        function addActivityCardEffects() {
            const cards = document.querySelectorAll('.activity-card');
            cards.forEach(card => {
                // Remover listeners existentes para evitar duplicação
                card.removeEventListener('mouseenter', handleCardHover);
                card.removeEventListener('mouseleave', handleCardLeave);
                
                // Adicionar novos listeners
                card.addEventListener('mouseenter', handleCardHover);
                card.addEventListener('mouseleave', handleCardLeave);
            });
        }
        
        function handleCardHover(event) {
            const card = event.currentTarget;
            card.style.transform = 'translateY(-4px) scale(1.02)';
            card.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.15), 0 8px 20px rgba(0, 0, 0, 0.1)';
            card.style.transition = 'all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        }
        
        function handleCardLeave(event) {
            const card = event.currentTarget;
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
        }
        
        // Destacar atividade sendo editada
        function highlightEditingActivity(id) {
            // Remover destaque anterior
            const previousHighlight = document.querySelector('.activity-card.editing');
            if (previousHighlight) {
                previousHighlight.classList.remove('editing');
            }
            
            // Adicionar destaque à atividade atual
            const currentCard = document.querySelector(`[data-activity-id="${id}"]`);
            if (currentCard) {
                currentCard.classList.add('editing');
                
                // Scroll suave para a atividade
                currentCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
        
        // Validação do formulário de edição
        function validateEditForm() {
            const titulo = document.getElementById('editTitulo').value.trim();
            
            if (!titulo) {
                showNotification('❌ O título é obrigatório', 'error');
                document.getElementById('editTitulo').focus();
                return false;
            }
            
            if (titulo.length > 255) {
                showNotification('❌ O título deve ter no máximo 255 caracteres', 'error');
                document.getElementById('editTitulo').focus();
                return false;
            }
            
            return true;
        }

        // Variável global para armazenar atividades
        let atividades = [];

        // Função para abrir overlay de exclusão estilo macOS
        function excluirAtividade(id) {
            console.log('Tentando excluir atividade ID:', id);
            console.log('Atividades disponíveis:', atividades);
            
            const atividade = atividades.find(a => a.id === id);
            if (!atividade) {
                console.error('Atividade não encontrada:', id);
                return;
            }
            
            console.log('Atividade encontrada:', atividade);
            showDeleteOverlay(atividade);
        }

        // Função para mostrar overlay de exclusão
        function showDeleteOverlay(atividade) {
            console.log('Tentando mostrar overlay para atividade:', atividade);
            
            const overlay = document.getElementById('deleteOverlay');
            const atividadeCard = document.getElementById('deleteAtividadeCard');
            
            if (!overlay) {
                console.error('Elemento deleteOverlay não encontrado!');
                return;
            }
            
            if (!atividadeCard) {
                console.error('Elemento deleteAtividadeCard não encontrado!');
                return;
            }
            
            console.log('Elementos encontrados, preenchendo dados...');
            
            // Preenchir dados da atividade
            atividadeCard.innerHTML = `
                <div class="delete-activity-header">
                    <i class="fas fa-tasks"></i>
                    <h6>${atividade.titulo}</h6>
                </div>
                <div class="delete-activity-body">
                    <p class="mb-1">${atividade.descricao || 'Sem descrição'}</p>
                    <div class="delete-activity-meta">
                        <span class="badge badge-${atividade.status}">${atividade.status}</span>
                        <span class="badge badge-priority-${atividade.prioridade}">${atividade.prioridade}</span>
                    </div>
                </div>
            `;
            
            // Configurar atributos de drag
            atividadeCard.setAttribute('data-id', atividade.id);
            atividadeCard.draggable = true;
            
            console.log('Mostrando overlay...');
            
            // Mostrar overlay
            overlay.style.display = 'flex';
            overlay.style.opacity = '0';
            
            // Animar entrada
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
                atividadeCard.style.transform = 'scale(1)';
                console.log('Overlay mostrado com sucesso!');
                
                // Reconfigurar drag & drop para garantir que funcione
                setupDragAndDropForCard();
            });
        }

        // Função para fechar overlay de exclusão
        function hideDeleteOverlay() {
            const overlay = document.getElementById('deleteOverlay');
            const atividadeCard = document.getElementById('deleteAtividadeCard');
            
            // Animar saída
            overlay.style.opacity = '0';
            atividadeCard.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                overlay.style.display = 'none';
                // Reset card
                atividadeCard.style.transform = 'scale(0.8)';
                atividadeCard.draggable = false;
                document.getElementById('deleteTrashIcon').classList.remove('active', 'highlight');
            }, 300);
        }

        // Função para excluir atividade via API
        async function performDelete(id) {
            try {
                const response = await fetch(`/api/atividades/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    carregarAtividades(); // Recarregar lista
                    showNotification('✅ Atividade excluída com sucesso!', 'success');
                    hideDeleteOverlay();
                } else {
                    showNotification('❌ Erro ao excluir atividade', 'error');
                }
            } catch (error) {
                console.error('Erro ao excluir atividade:', error);
                showNotification('❌ Erro de conexão', 'error');
            }
        }

        // Sistema de Drag & Drop
        function setupDragAndDrop() {
            console.log('Configurando sistema de drag & drop...');
            
            const atividadeCard = document.getElementById('deleteAtividadeCard');
            const trashIcon = document.getElementById('deleteTrashIcon');
            const arrow = document.querySelector('.delete-arrow');
            
            console.log('Elementos drag & drop:');
            console.log('- Card:', atividadeCard ? 'OK' : 'NÃO ENCONTRADO');
            console.log('- Trash:', trashIcon ? 'OK' : 'NÃO ENCONTRADO');
            console.log('- Arrow:', arrow ? 'OK' : 'NÃO ENCONTRADO');
            
            if (!atividadeCard || !trashIcon || !arrow) {
                console.warn('Alguns elementos não foram encontrados, drag & drop pode não funcionar');
                return;
            }
            
            setupDragAndDropEvents(atividadeCard, trashIcon, arrow);
        }

        // Configurar drag & drop específico para o card quando overlay é mostrado
        function setupDragAndDropForCard() {
            console.log('Reconfigurando drag & drop para o card...');
            
            const atividadeCard = document.getElementById('deleteAtividadeCard');
            const trashIcon = document.getElementById('deleteTrashIcon');
            const arrow = document.querySelector('.delete-arrow');
            
            if (!atividadeCard || !trashIcon || !arrow) {
                console.error('Elementos não encontrados para reconfigurar drag & drop');
                return;
            }
            
            setupDragAndDropEvents(atividadeCard, trashIcon, arrow);
        }

        // Função para configurar os eventos de drag & drop
        function setupDragAndDropEvents(atividadeCard, trashIcon, arrow) {
            console.log('Configurando eventos de drag & drop...');
            
            // Limpar eventos existentes (clonando elemento)
            const newCard = atividadeCard.cloneNode(true);
            atividadeCard.parentNode.replaceChild(newCard, atividadeCard);
            
            // Atualizar referência
            const card = document.getElementById('deleteAtividadeCard');
            
            let isDragging = false;
            
            console.log('Adicionando evento dragstart...');
            
            // Drag start
            card.addEventListener('dragstart', function(e) {
                console.log('Dragstart disparado!');
                isDragging = true;
                this.classList.add('dragging');
                trashIcon.classList.add('active');
                arrow.classList.add('active');
                
                // Permitir drop
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.outerHTML);
            });
            
            // Drag end
            card.addEventListener('dragend', function(e) {
                console.log('Dragend disparado!');
                this.classList.remove('dragging');
                if (!trashIcon.classList.contains('highlight')) {
                    trashIcon.classList.remove('active');
                    arrow.classList.remove('active');
                }
                isDragging = false;
            });
            
            console.log('Adicionando eventos da lixeira...');
            
            // Trash drop zone events
            trashIcon.addEventListener('dragover', function(e) {
                console.log('Dragover na lixeira!');
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('highlight');
            });
            
            trashIcon.addEventListener('dragleave', function(e) {
                console.log('Dragleave da lixeira!');
                this.classList.remove('highlight');
            });
            
            trashIcon.addEventListener('drop', function(e) {
                console.log('Drop na lixeira!');
                e.preventDefault();
                this.classList.add('highlight');
                
                // Animação de sucesso
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
                
                // Pegar ID da atividade
                const atividadeId = card.getAttribute('data-id');
                console.log('ID da atividade para excluir:', atividadeId);
                
                // Excluir após animação
                setTimeout(() => {
                    performDelete(atividadeId);
                }, 500);
            });
            
            console.log('Drag & drop configurado com sucesso!');
        }

        // Variáveis globais para filtros
        let atividadesOriginais = [];
        let filtrosAtivos = {
            status: '',
            prioridade: '',
            busca: ''
        };

        // Função para aplicar filtros
        function aplicarFiltros() {
            console.log('Aplicando filtros:', filtrosAtivos);
            
            // Se não há atividades carregadas, não fazer nada
            if (!atividadesOriginais.length) {
                console.log('Nenhuma atividade para filtrar');
                return;
            }
            
            let atividadesFiltradas = [...atividadesOriginais];
            
            // Filtro por status
            if (filtrosAtivos.status) {
                atividadesFiltradas = atividadesFiltradas.filter(atividade => 
                    atividade.status === filtrosAtivos.status
                );
                console.log(`Filtro status '${filtrosAtivos.status}': ${atividadesFiltradas.length} atividades`);
            }
            
            // Filtro por prioridade
            if (filtrosAtivos.prioridade) {
                atividadesFiltradas = atividadesFiltradas.filter(atividade => 
                    atividade.prioridade === filtrosAtivos.prioridade
                );
                console.log(`Filtro prioridade '${filtrosAtivos.prioridade}': ${atividadesFiltradas.length} atividades`);
            }
            
            // Filtro por busca de texto
            if (filtrosAtivos.busca) {
                const termoBusca = filtrosAtivos.busca.toLowerCase();
                atividadesFiltradas = atividadesFiltradas.filter(atividade => 
                    atividade.titulo.toLowerCase().includes(termoBusca) ||
                    (atividade.descricao && atividade.descricao.toLowerCase().includes(termoBusca))
                );
                console.log(`Filtro busca '${filtrosAtivos.busca}': ${atividadesFiltradas.length} atividades`);
            }
            
            console.log(`Total após filtros: ${atividadesFiltradas.length} de ${atividadesOriginais.length} atividades`);
            
            // Renderizar atividades filtradas
            renderizarAtividades(atividadesFiltradas);
            
            // Mostrar status dos filtros
            mostrarStatusFiltros(atividadesFiltradas.length, atividadesOriginais.length);
        }

        // Função para mostrar status dos filtros
        function mostrarStatusFiltros(filtradas, total) {
            const container = document.getElementById('listaAtividades');
            const temFiltroAtivo = filtrosAtivos.status || filtrosAtivos.prioridade || filtrosAtivos.busca;
            
            // Remover badge anterior se existir
            const badgeExistente = container.querySelector('.filter-status-badge');
            if (badgeExistente) {
                badgeExistente.remove();
            }
            
            if (temFiltroAtivo && filtradas < total) {
                // Adicionar badge de status dos filtros
                const statusHtml = `
                    <div class="filter-status-badge mb-3">
                        <div class="glass-card p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="glass-text small">
                                    <i class="fas fa-filter me-2" style="color: #00ffff;"></i>
                                    Mostrando ${filtradas} de ${total} atividades
                                </span>
                                <button class="btn btn-sm glass-button-sm" onclick="limparTodosFiltros()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('afterbegin', statusHtml);
            }
        }

        // Event listeners para filtros
        function configurarEventosFiltros() {
            // Filtro por status
            document.getElementById('filtroStatus').addEventListener('change', function() {
                filtrosAtivos.status = this.value;
                console.log('Status mudou para:', this.value);
                aplicarFiltros();
            });

            // Filtro por prioridade  
            document.getElementById('filtroPrioridade').addEventListener('change', function() {
                filtrosAtivos.prioridade = this.value;
                console.log('Prioridade mudou para:', this.value);
                aplicarFiltros();
            });

            // Filtro por busca (com debounce)
            let timeoutBusca;
            document.getElementById('buscar').addEventListener('input', function() {
                clearTimeout(timeoutBusca);
                timeoutBusca = setTimeout(() => {
                    filtrosAtivos.busca = this.value.trim();
                    console.log('Busca mudou para:', this.value);
                    aplicarFiltros();
                }, 300); // Aguarda 300ms após o usuário parar de digitar
            });

            // Limpar filtros com Enter na busca
            document.getElementById('buscar').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    aplicarFiltros();
                }
            });
        }

        // Função para limpar todos os filtros
        function limparTodosFiltros() {
            console.log('Limpando todos os filtros...');
            
            // Resetar formulários
            document.getElementById('filtroStatus').value = '';
            document.getElementById('filtroPrioridade').value = '';
            document.getElementById('buscar').value = '';
            
            // Resetar filtros ativos
            filtrosAtivos = {
                status: '',
                prioridade: '',
                busca: ''
            };
            
            // Mostrar todas as atividades
            renderizarAtividades(atividadesOriginais);
            
            // Remover badge de status
            const badgeExistente = document.querySelector('.filter-status-badge');
            if (badgeExistente) {
                badgeExistente.remove();
            }
            
            // Feedback para o usuário
            showNotification('🔄 Filtros limpos com sucesso!', 'success');
        }

        // Limpar filtros - Event listener para o botão
        document.getElementById('limparFiltros').addEventListener('click', limparTodosFiltros);

        // Carregar atividades ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔄 Iniciando carregamento das atividades...');
            carregarAtividades();
        });

        // Debug: Log dos filtros quando mudam
        window.debugFiltros = function() {
            console.log('🔍 Estado atual dos filtros:');
            console.log('- Status:', filtrosAtivos.status || 'Todos');
            console.log('- Prioridade:', filtrosAtivos.prioridade || 'Todas');
            console.log('- Busca:', filtrosAtivos.busca || 'Nenhuma');
            console.log('- Total de atividades originais:', atividadesOriginais.length);
        };
        
        console.log('✨ Página de Atividades Neon carregada com sucesso!');
        console.log('🎯 Sistema de Edição Animada ativado:');
        console.log('   • Clique no botão ✏️ para editar uma atividade');
        console.log('   • Use Ctrl+S para salvar rapidamente');
        console.log('   • Use Esc para cancelar edição');
        console.log('   • Use Ctrl+N para nova atividade');
    </script>
    <!-- Overlay de Exclusão estilo macOS -->
    <div id="deleteOverlay" class="delete-overlay">
        <div class="delete-container">
            <!-- Título do overlay -->
            <div class="delete-header">
                <h4><i class="fas fa-trash-alt me-2"></i>Remover Atividade</h4>
                <p class="text-muted">Arraste a atividade para a lixeira para excluí-la</p>
            </div>
            
            <!-- Área principal -->
            <div class="delete-main">
                <!-- Card da atividade (esquerda) -->
                <div class="delete-item">
                    <div id="deleteAtividadeCard" class="delete-activity-card">
                        <!-- Conteúdo será preenchido dinamicamente -->
                    </div>
                </div>
                
                <!-- Seta do meio -->
                <div class="delete-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                
                <!-- Lixeira (direita) -->
                <div class="delete-target">
                    <div id="deleteTrashIcon" class="delete-trash">
                        <i class="fas fa-trash-alt"></i>
                        <span>Lixeira</span>
                    </div>
                </div>
            </div>
            
            <!-- Botões de ação -->
            <div class="delete-actions">
                <button type="button" class="btn btn-secondary" onclick="hideDeleteOverlay()">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Inicializar sistema de drag & drop quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, inicializando drag & drop...');
            setupDragAndDrop();
        });
        
        // Fechar overlay com Esc
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDeleteOverlay();
            }
        });
        
        // Debug: Verificar se elementos existem após carregamento
        setTimeout(() => {
            const overlay = document.getElementById('deleteOverlay');
            const card = document.getElementById('deleteAtividadeCard');
            const trash = document.getElementById('deleteTrashIcon');
            
            console.log('Verificação dos elementos:');
            console.log('- Overlay:', overlay ? 'OK' : 'NÃO ENCONTRADO');
            console.log('- Card:', card ? 'OK' : 'NÃO ENCONTRADO');
            console.log('- Trash:', trash ? 'OK' : 'NÃO ENCONTRADO');
            
            // Teste simples - criar botão de teste
            if (window.location.search.includes('debug=1')) {
                const testBtn = document.createElement('button');
                testBtn.innerHTML = '🧪 Teste Overlay';
                testBtn.style.position = 'fixed';
                testBtn.style.top = '10px';
                testBtn.style.right = '10px';
                testBtn.style.zIndex = '9999';
                testBtn.style.backgroundColor = '#ff0000';
                testBtn.style.color = 'white';
                testBtn.style.border = 'none';
                testBtn.style.padding = '10px';
                testBtn.style.borderRadius = '5px';
                testBtn.onclick = function() {
                    console.log('Teste: criando atividade fake...');
                    const atividadeFake = {
                        id: 999,
                        titulo: 'Atividade de Teste',
                        descricao: 'Esta é uma atividade de teste para verificar o overlay',
                        status: 'pendente',
                        prioridade: 'alta'
                    };
                    showDeleteOverlay(atividadeFake);
                };
                document.body.appendChild(testBtn);
                console.log('Botão de teste criado! Adicione ?debug=1 na URL para ver.');
            }
        }, 1000);
    </script>

</body>
</html> 
@extends('layouts.app')

@section('title', 'Dashboard - DashMEBoard')

@section('content')
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
                        <a href="#atividades" class="nav-link-item" onclick="navigateToSection('atividades')">
                            <i class="fas fa-tasks" style="color: #06b6d4;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Atividades</div>
                                <div class="nav-link-description">Gerencie suas tarefas diárias</div>
                            </div>
                        </a>
                        
                        <a href="#projetos" class="nav-link-item" onclick="navigateToSection('projetos')">
                            <i class="fas fa-folder" style="color: #f59e0b;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Projetos</div>
                                <div class="nav-link-description">Organize seus projetos</div>
                            </div>
                        </a>
                        
                        <a href="#estatisticas" class="nav-link-item" onclick="navigateToSection('estatisticas')">
                            <i class="fas fa-chart-pie" style="color: #ef4444;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Estatísticas</div>
                                <div class="nav-link-description">Analise sua produtividade</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('atividades') }}" class="nav-link-item">
                            <i class="fas fa-list" style="color: #8b5cf6;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Lista de Atividades</div>
                                <div class="nav-link-description">Veja todas suas atividades</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('profiles.my-profile') }}" class="nav-link-item">
                            <i class="fas fa-user" style="color: #10b981;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Meu Perfil</div>
                                <div class="nav-link-description">Configurações e estatísticas</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('fortune-cookie') }}" class="nav-link-item">
                            <i class="fas fa-cookie-bite" style="color: #ffd700;"></i>
                            <div class="nav-link-content">
                                <div class="nav-link-title">Biscoito da Sorte</div>
                                <div class="nav-link-description">Quebre o biscoito e escolha seu fundo</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Fullscreen Overlay -->
    <div class="fullscreen-menu" id="fullscreenMenu">
        <div class="close-menu-btn" onclick="closeFullscreenMenu()"></div>
        <div class="fullscreen-menu-content">
            <!-- Relógio em tempo real -->
            <div class="fullscreen-clock" id="fullscreenClock"></div>
            
            <!-- Seções do menu -->
            <div class="menu-sections">
                <!-- Navegação -->
                <div class="menu-section">
                    <h3>🧭 Navegação</h3>
                    <div class="menu-section-links">
                        <a href="{{ route('dashboard') }}" class="menu-section-link">🏠 Dashboard</a>
                        <a href="#atividades" class="menu-section-link" onclick="navigateToSection('atividades')">📋 Atividades</a>
                        <a href="#projetos" class="menu-section-link" onclick="navigateToSection('projetos')">📁 Projetos</a>
                        <a href="#estatisticas" class="menu-section-link" onclick="navigateToSection('estatisticas')">📊 Estatísticas</a>
                    </div>
                </div>
                
                <!-- Ações Rápidas -->
                <div class="menu-section">
                    <h3>⚡ Ações Rápidas</h3>
                    <div class="menu-section-links">
                        <a href="#nova-atividade" class="menu-section-link">➕ Nova Atividade</a>
                        <a href="#novo-projeto" class="menu-section-link">📁 Novo Projeto</a>
                        <a href="{{ route('fortune-cookie') }}" class="menu-section-link">🍪 Biscoito da Sorte</a>
                        <a href="#buscar" class="menu-section-link">🔍 Buscar</a>
                        <a href="#filtros" class="menu-section-link">🎛️ Filtros</a>
                    </div>
                </div>
                
                <!-- Configurações -->
                <div class="menu-section">
                    <h3>⚙️ Configurações</h3>
                    <div class="menu-section-links">
                        <a href="{{ route('profiles.my-profile') }}" class="menu-section-link">👤 Perfil</a>
                        <a href="#preferencias" class="menu-section-link">🎨 Preferências</a>
                        <a href="#notificacoes" class="menu-section-link">🔔 Notificações</a>
                        <a href="#ajuda" class="menu-section-link">❓ Ajuda</a>
                    </div>
                </div>
                
                <!-- Sistema -->
                <div class="menu-section">
                    <h3>🛠️ Sistema</h3>
                    <div class="menu-section-links">
                        <a href="#backup" class="menu-section-link">💾 Backup</a>
                        <a href="#importar" class="menu-section-link">📥 Importar</a>
                        <a href="#exportar" class="menu-section-link">📤 Exportar</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="menu-section-link" style="border: none; background: none; width: 100%; text-align: left;">
                                🚪 Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4 glass-container">
        <!-- Card Principal com Glassmorphism -->
        <div class="main-content-card">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col">
                    <h1 class="h3 glass-text">✨ Bem-vindo ao DashMEBoard, {{ $user->name }}!</h1>
                                         <p class="glass-text-muted">Sua interface de produtividade em alta definição</p>
                </div>
            </div>

                    <!-- Estatísticas Grid -->
        <div id="estatisticas">
            <div class="section-title">
                <div class="section-icon">📊</div>
                <h2 class="glass-text mb-0">Painel de Controle</h2>
            </div>
            
            <div class="stats-grid">
                <div class="stats-card text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 glass-text">{{ $atividadesCount }}</h3>
                            <p class="mb-0 glass-text-muted">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x" style="color: #6366f1; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 glass-text">{{ $atividades->where('status', 'pendente')->count() }}</h3>
                            <p class="mb-0 glass-text-muted">Pendentes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x" style="color: #f59e0b; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 glass-text">{{ $atividades->where('status', 'em_andamento')->count() }}</h3>
                            <p class="mb-0 glass-text-muted">Em Progresso</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-play fa-2x" style="color: #06b6d4; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stats-card text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 glass-text">{{ $atividades->where('status', 'concluida')->count() }}</h3>
                            <p class="mb-0 glass-text-muted">Concluídas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x" style="color: #10b981; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biscoito da Sorte (Uma vez por dia) -->
        <div class="fortune-cookie-dashboard-section mb-5" id="fortuneCookieSection" style="display: none;">
            <div class="section-title">
                <div class="section-icon">🍪</div>
                <h2 class="glass-text mb-0">Biscoito da Sorte do Dia</h2>
            </div>
            <div class="grid-large">
                <div class="section-card fortune-cookie-card">
                    <div class="text-center">
                        <div class="fortune-cookie-icon mb-3">
                            <i class="fas fa-cookie-bite fa-4x" style="color: #ffd700; text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);"></i>
                        </div>
                        <h4 class="glass-text mb-3">Quebre o biscoito e descubra sua sorte!</h4>
                        <p class="glass-text-muted mb-4">Receba uma mensagem inspiradora e personalize seu perfil.</p>
                        <div class="fortune-message-preview mb-3" id="fortuneMessagePreview" style="display: none;">
                            <div class="glass-card p-3">
                                <p class="glass-text mb-2 fst-italic" id="previewMessage"></p>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-glass-fortune me-2" id="breakCookieBtn">
                                <i class="fas fa-cookie-bite me-2"></i>Quebrar Biscoito
                            </button>
                            <a href="{{ route('profiles.my-profile') }}" class="btn btn-glass-secondary" id="goToProfileBtn" style="display: none;">
                                <i class="fas fa-user me-2"></i>Ver Meu Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividades -->
        <div id="atividades">
            <div class="section-title">
                <div class="section-icon">📋</div>
                <h2 class="glass-text mb-0">Centro de Atividades</h2>
            </div>
            
                            <div class="grid-large">
                <!-- Atividades Recentes -->
                <div class="section-card">
                    <h5 class="glass-text mb-4">
                        <i class="fas fa-list me-2" style="color: #6366f1; opacity: 0.8;"></i>
                        Atividades Recentes
                    </h5>
                        @if($atividades->count() > 0)
                            <div class="space-y-3">
                                @foreach($atividades->take(5) as $atividade)
                                    <div class="activity-card p-3 mb-3">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <h6 class="mb-2 glass-text">{{ $atividade->titulo }}</h6>
                                            <small class="glass-text-muted">{{ $atividade->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-2 glass-text-muted">{{ Str::limit($atividade->descricao, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="glass-badge badge me-2">
                                                    {{ ucfirst(str_replace('_', ' ', $atividade->status)) }}
                                                </span>
                                                <span class="glass-badge badge">
                                                    {{ ucfirst($atividade->prioridade) }}
                                                </span>
                                            </div>
                                            @if($atividade->data_limite)
                                                <small class="glass-text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $atividade->data_limite->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-center">
                                <a href="/atividades" class="btn btn-glass">
                                    <i class="fas fa-arrow-right me-2"></i>Ver todas as Atividades
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x glass-text-muted mb-3"></i>
                                <p class="glass-text-muted">Nenhuma atividade encontrada.</p>
                                <a href="/atividades" class="btn btn-glass">
                                    <i class="fas fa-plus me-2"></i>Criar primeira atividade
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Painel de Prioridades -->
                <div class="section-card">
                    <h5 class="glass-text mb-4">
                        <i class="fas fa-chart-pie me-2" style="color: #f59e0b; opacity: 0.8;"></i>
                        Central de Prioridades
                    </h5>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stats-card p-3 mb-3">
                                    <h3 class="mb-1 glass-text" style="color: #ef4444;">{{ $atividades->where('prioridade', 'alta')->count() }}</h3>
                                    <small class="glass-text-muted">Alta</small>
                                    <div class="mt-2">
                                        <i class="fas fa-arrow-up glass-icon" style="color: #ef4444;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-card p-3 mb-3">
                                    <h3 class="mb-1 glass-text" style="color: #f59e0b;">{{ $atividades->where('prioridade', 'media')->count() }}</h3>
                                    <small class="glass-text-muted">Média</small>
                                    <div class="mt-2">
                                        <i class="fas fa-minus glass-icon" style="color: #f59e0b;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-card p-3 mb-3">
                                    <h3 class="mb-1 glass-text" style="color: #06b6d4;">{{ $atividades->where('prioridade', 'baixa')->count() }}</h3>
                                    <small class="glass-text-muted">Baixa</small>
                                    <div class="mt-2">
                                        <i class="fas fa-arrow-down glass-icon" style="color: #06b6d4;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($atividades->where('prioridade', 'alta')->where('status', '!=', 'concluida')->count() > 0)
                            <div class="activity-card p-3 mt-3" style="border-left: 4px solid #ef4444;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-3 glass-icon" style="color: #ef4444; font-size: 1.5rem;"></i>
                                    <div>
                                        <p class="mb-1 glass-text">
                                            <strong>{{ $atividades->where('prioridade', 'alta')->where('status', '!=', 'concluida')->count() }}</strong> atividade(s) de alta prioridade pendente(s)!
                                        </p>
                                        <small class="glass-text-muted">Recomendamos focar nestas primeiro</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="activity-card p-3 mt-3 text-center" style="border-left: 4px solid #10b981;">
                                <i class="fas fa-check-circle me-2 glass-icon" style="color: #10b981;"></i>
                                <span class="glass-text">Parabéns! Todas as prioridades altas estão concluídas! 🎉</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Projetos Neon - SEÇÃO COMENTADA -->
        <!--
        <div class="row mt-5" id="projetos">
            <div class="col-12">
                <div class="section-title mb-4 text-center">
                    <div class="section-icon mx-auto mb-3">📁</div>
                    <h2 class="glass-text mb-0">Gerenciador de Projetos</h2>
                                          <p class="glass-text-muted">Organize seus projetos eficientemente</p>
                </div>
                
                Grid de Projetos Centralizado
                <div class="d-flex justify-content-center">
                    <div class="row g-4 justify-content-center align-items-start">
                        Card Novo Projeto
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="glass-card project-card w-100" onclick="openProjectModal()">
                                <div class="card-body text-center p-3">
                                    <div class="project-header d-flex justify-content-between align-items-start mb-2">
                                        <div class="project-icon">
                                            <i class="fas fa-plus-circle fa-2x" style="color: #00ffff;"></i>
                                        </div>
                                        <span class="glass-badge project-status-new">Novo</span>
                                    </div>
                                    <h6 class="glass-text mb-1">✨ Criar Novo Projeto</h6>
                                    <p class="glass-text-muted small mb-2">Inicie um novo projeto ou importe via JSON</p>
                                    <div class="project-actions">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <button class="btn glass-button btn-sm project-btn" onclick="event.stopPropagation(); createNewProject()">
                                                <i class="fas fa-magic me-1"></i>Criar
                                            </button>
                                            <button class="btn glass-button btn-sm project-btn" onclick="event.stopPropagation(); document.getElementById('jsonUpload').click()">
                                                <i class="fas fa-file-upload me-1"></i>JSON
                                            </button>
                                        </div>
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <button class="btn glass-button btn-sm project-btn-small" onclick="event.stopPropagation(); showJsonExample()">
                                                <i class="fas fa-eye me-1"></i>Tutorial
                                            </button>
                                            <button class="btn glass-button btn-sm project-btn-small" onclick="event.stopPropagation(); downloadJsonExample()">
                                                <i class="fas fa-download me-1"></i>Modelo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        Projeto Atual
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="glass-card project-card w-100">
                                <div class="card-body p-3">
                                    <div class="project-header d-flex justify-content-between align-items-start mb-2">
                                        <div class="project-icon">
                                            <i class="fas fa-rocket fa-2x" style="color: #ff6b6b;"></i>
                                        </div>
                                        <span class="glass-badge project-status">Em Andamento</span>
                                    </div>
                                    <h6 class="glass-text mb-1">🌟 DashMEBoard Neon</h6>
                                    <p class="glass-text-muted small mb-2">Sistema de gerenciamento de tarefas</p>
                                    <div class="project-stats">
                                        <div class="stat-item mb-1">
                                            <i class="fas fa-tasks me-2" style="color: #00ffff;"></i>
                                            <span class="glass-text-muted small">{{ $atividadesCount }} atividades</span>
                                        </div>
                                        <div class="stat-item mb-2">
                                            <i class="fas fa-calendar me-2" style="color: #10b981;"></i>
                                            <span class="glass-text-muted small">Criado hoje</span>
                                        </div>
                                    </div>
                                    <div class="project-progress">
                                        <div class="progress glass-progress mb-1" style="height: 6px;">
                                            <div class="progress-bar" style="width: 75%; background: linear-gradient(90deg, #00ffff, #ff6b6b);"></div>
                                        </div>
                                        <small class="glass-text-muted">75% completo</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        Próximos Projetos
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="glass-card project-card w-100">
                                <div class="card-body p-3">
                                    <div class="project-header d-flex justify-content-between align-items-start mb-2">
                                        <div class="project-icon">
                                            <i class="fas fa-lightbulb fa-2x" style="color: #f59e0b;"></i>
                                        </div>
                                        <span class="glass-badge project-status-planning">Planejamento</span>
                                    </div>
                                    <h6 class="glass-text mb-1">🚀 Futuras Ideias</h6>
                                    <p class="glass-text-muted small mb-2">Espaço para planejar seus próximos desenvolvimentos</p>
                                    <div class="project-ideas">
                                        <div class="idea-item mb-1">
                                            <i class="fas fa-star me-2" style="color: #f59e0b;"></i>
                                            <span class="glass-text-muted small">Dashboard Analytics</span>
                                        </div>
                                        <div class="idea-item mb-1">
                                            <i class="fas fa-mobile-alt me-2" style="color: #f59e0b;"></i>
                                            <span class="glass-text-muted small">App Mobile</span>
                                        </div>
                                        <div class="idea-item">
                                            <i class="fas fa-brain me-2" style="color: #f59e0b;"></i>
                                            <span class="glass-text-muted small">Integração</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        -->
        <!-- FIM DA SEÇÃO COMENTADA - GERENCIADOR DE PROJETOS -->
                
                <!-- Input oculto para upload JSON -->
                <input type="file" id="jsonUpload" accept=".json" style="display: none;" onchange="handleJsonUpload(this)">
                
                <!-- Footer Neon -->
                <footer class="neon-footer mt-5">
                    <div class="footer-content">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="footer-branding">
                                    <h5 class="glass-text mb-2">
                                        <i class="fas fa-tasks me-2" style="color: #00ffff;"></i>
                                        DashMEBoard
                                    </h5>
                                    <p class="glass-text-muted small mb-0">
                                        Sistema de gerenciamento de tarefas
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="footer-stats text-md-end">
                                    <div class="d-flex justify-content-md-end justify-content-start flex-wrap gap-3">
                                        <div class="footer-stat">
                                            <div class="stat-icon">
                                                <i class="fas fa-tasks" style="color: #06b6d4;"></i>
                                            </div>
                                            <div class="stat-text">
                                                <div class="stat-number">{{ $atividadesCount }}</div>
                                                <div class="stat-label">Atividades</div>
                                            </div>
                                        </div>
                                        <div class="footer-stat">
                                            <div class="stat-icon">
                                                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                            </div>
                                            <div class="stat-text">
                                                <div class="stat-number">{{ $atividades->where('status', 'concluida')->count() }}</div>
                                                <div class="stat-label">Concluídas</div>
                                            </div>
                                        </div>
                                        <div class="footer-stat">
                                            <div class="stat-icon">
                                                <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                            </div>
                                            <div class="stat-text">
                                                <div class="stat-number">{{ $atividades->where('status', 'pendente')->count() }}</div>
                                                <div class="stat-label">Pendentes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="footer-divider"></div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="footer-links">
                                    <a href="{{ route('atividades') }}" class="footer-link">
                                        <i class="fas fa-list me-1"></i>Atividades
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="footer-link">
                                        <i class="fas fa-home me-1"></i>Dashboard
                                    </a>
                                    <a href="{{ route('profiles.my-profile') }}" class="footer-link">
                                        <i class="fas fa-user me-1"></i>Perfil
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="footer-copyright">
                                    <p class="glass-text-muted small mb-0">
                                        &copy; {{ date('Y') }} DashMEBoard. Sistema de gerenciamento de tarefas.
                                    </p>
                                    <div class="footer-version">
                                        <span class="version-badge">v2.0 Neon</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        </div> <!-- Fim do main-content-card -->
    </div>
@endsection

@section('scripts')
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
        
        // Função para atualizar dashboard
        function refreshDashboard() {
            closeDropdownMenu();
            location.reload();
        }
        
        // Funcionalidades de Projeto
        function createNewProject() {
            const projectName = prompt('✨ Digite o nome do novo projeto:');
            if (projectName) {
                console.log('Criando projeto:', projectName);
                // Aqui você pode adicionar a lógica para criar o projeto
                alert(`🚀 Projeto "${projectName}" criado com sucesso!`);
            }
        }
        
        function openProjectModal() {
            console.log('📁 Abrindo modal de projeto...');
            // Implementar modal de detalhes do projeto
        }
        
        async function handleJsonUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            try {
                const reader = new FileReader();
                reader.onload = async function(e) {
                    try {
                        const jsonData = JSON.parse(e.target.result);
                        console.log('📄 JSON carregado:', jsonData);
                        
                        // Validar estrutura do JSON
                        if (validateProjectJson(jsonData)) {
                            let projectToImport = jsonData;
                            
                            // Converter se for formato Trello
                            if (isTrelloFormat(jsonData)) {
                                console.log('🎯 Convertendo dados do Trello...');
                                projectToImport = convertTrelloToInternalFormat(jsonData);
                                
                                const activeCards = jsonData.cards.filter(card => !card.closed).length;
                                const totalCards = jsonData.cards.length;
                                
                                showProjectNotification(
                                    '🎯 Board Trello detectado!',
                                    `"${jsonData.name}" | ${activeCards} cards ativos de ${totalCards} totais | ${jsonData.lists.length} listas`,
                                    'info'
                                );
                            }
                            
                            await importProjectFromJson(projectToImport);
                        } else {
                            showProjectNotification(
                                '❌ Formato JSON inválido',
                                'Suportamos: Formato DashMEBoard ou export JSON do Trello. Verifique se o arquivo está correto.',
                                'error'
                            );
                        }
                    } catch (error) {
                        console.error('Erro no parse JSON:', error);
                        showProjectNotification(
                            '❌ Erro ao ler arquivo JSON',
                            'Arquivo JSON inválido ou corrompido. Verifique a sintaxe.',
                            'error'
                        );
                    }
                };
                reader.readAsText(file);
            } catch (error) {
                showProjectNotification(
                    '❌ Erro no upload',
                    'Erro ao processar o arquivo',
                    'error'
                );
            }
            
            // Limpar o input para permitir novo upload do mesmo arquivo
            input.value = '';
        }
        
        function validateProjectJson(data) {
            // Validar se o JSON tem a estrutura esperada
            if (!data || typeof data !== 'object') return false;
            
            // Detectar formato Trello
            if (isTrelloFormat(data)) {
                console.log('🎯 Detectado formato Trello');
                return true;
            }
            
            // Validar formato interno
            if (!data.name || typeof data.name !== 'string') return false;
            if (!data.description || typeof data.description !== 'string') return false;
            if (!Array.isArray(data.tasks)) return false;
            
            // Validar estrutura das tarefas
            for (const task of data.tasks) {
                if (!task.title || typeof task.title !== 'string') return false;
                if (task.priority && !['baixa', 'media', 'alta'].includes(task.priority)) return false;
                if (task.status && !['pendente', 'em_andamento', 'concluida'].includes(task.status)) return false;
            }
            
            return true;
        }

        function isTrelloFormat(data) {
            // Verificar se é formato de board do Trello
            return data.hasOwnProperty('lists') && Array.isArray(data.lists) && 
                   data.hasOwnProperty('cards') && Array.isArray(data.cards) &&
                   data.hasOwnProperty('name');
        }

        function convertTrelloToInternalFormat(trelloData) {
            console.log('🔄 Convertendo dados do Trello:', trelloData);
            
            const project = {
                name: trelloData.name || 'Projeto Importado do Trello',
                description: trelloData.desc || `Projeto importado do Trello em ${new Date().toLocaleDateString()}`,
                tasks: []
            };

            // Verificar se listas existem
            if (!trelloData.lists || !Array.isArray(trelloData.lists)) {
                console.warn('⚠️ Listas não encontradas no JSON do Trello');
                trelloData.lists = [];
            }

            // Verificar se cards existem
            if (!trelloData.cards || !Array.isArray(trelloData.cards)) {
                console.warn('⚠️ Cards não encontrados no JSON do Trello');
                trelloData.cards = [];
            }

            // Mapear listas para status
            const listStatusMap = {};
            const listNames = {};
            
            trelloData.lists.forEach(list => {
                const listNameLower = (list.name || '').toLowerCase();
                listNames[list.id] = list.name;
                
                if (listNameLower.includes('to do') || listNameLower.includes('pendente') || 
                    listNameLower.includes('backlog') || listNameLower.includes('todo') ||
                    listNameLower.includes('para fazer')) {
                    listStatusMap[list.id] = 'pendente';
                } else if (listNameLower.includes('doing') || listNameLower.includes('em andamento') || 
                           listNameLower.includes('progress') || listNameLower.includes('fazendo') ||
                           listNameLower.includes('in progress')) {
                    listStatusMap[list.id] = 'em_andamento';
                } else if (listNameLower.includes('done') || listNameLower.includes('concluido') || 
                           listNameLower.includes('finished') || listNameLower.includes('completo') ||
                           listNameLower.includes('finalizado')) {
                    listStatusMap[list.id] = 'concluida';
                } else {
                    listStatusMap[list.id] = 'pendente'; // Status padrão
                }
            });

            console.log('📋 Mapeamento de listas:', listNames);
            console.log('✅ Status mapeados:', listStatusMap);

            // Processar cards
            let processedCount = 0;
            let skippedCount = 0;

            trelloData.cards.forEach(card => {
                if (!card.closed && card.name && card.name.trim() !== '') { // Apenas cards não arquivados e com nome
                    const task = {
                        title: (card.name || 'Tarefa sem título').substring(0, 255), // Limitar título
                        description: (card.desc || '').substring(0, 1000), // Limitar descrição
                        status: listStatusMap[card.idList] || 'pendente',
                        priority: mapTrelloPriority(card),
                        deadline: formatTrelloDate(card.due)
                    };
                    
                    project.tasks.push(task);
                    processedCount++;
                } else {
                    skippedCount++;
                }
            });

            console.log(`✅ Processados: ${processedCount} cards, Ignorados: ${skippedCount} cards`);
            console.log('✅ Dados convertidos:', project);
            return project;
        }

        function formatTrelloDate(dateString) {
            if (!dateString) return null;
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return null;
                
                return date.toISOString().split('T')[0];
            } catch (error) {
                console.warn('⚠️ Data inválida do Trello:', dateString);
                return null;
            }
        }

        function mapTrelloPriority(card) {
            // Mapear prioridade baseado em labels, cores ou palavras-chave
            const cardText = ((card.name || '') + ' ' + (card.desc || '')).toLowerCase();
            
            // Verificar labels primeiro (mais confiável)
            if (card.labels && Array.isArray(card.labels)) {
                for (const label of card.labels) {
                    const labelName = (label.name || '').toLowerCase();
                    const labelColor = (label.color || '').toLowerCase();
                    
                    // Prioridade ALTA
                    if (labelName.includes('urgent') || labelName.includes('alta') || 
                        labelName.includes('high') || labelName.includes('critical') ||
                        labelName.includes('importante') || labelName.includes('priority') ||
                        labelColor === 'red' || labelColor === 'orange') {
                        return 'alta';
                    }
                    
                    // Prioridade BAIXA
                    if (labelName.includes('low') || labelName.includes('baixa') || 
                        labelName.includes('opcional') || labelName.includes('minor') ||
                        labelName.includes('nice to have') || labelName.includes('future') ||
                        labelColor === 'green' || labelColor === 'blue') {
                        return 'baixa';
                    }
                    
                    // Prioridade MÉDIA
                    if (labelName.includes('medium') || labelName.includes('media') || 
                        labelName.includes('normal') || labelName.includes('regular') ||
                        labelColor === 'yellow') {
                        return 'media';
                    }
                }
            }

            // Verificar due date (cards com prazo mais próximo = prioridade maior)
            if (card.due) {
                const dueDate = new Date(card.due);
                const today = new Date();
                const diffDays = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
                
                if (diffDays <= 3) return 'alta';    // Vence em 3 dias ou menos
                if (diffDays <= 7) return 'media';   // Vence em uma semana
            }

            // Verificar texto do card
            if (cardText.includes('urgent') || cardText.includes('priority') || 
                cardText.includes('important') || cardText.includes('asap') ||
                cardText.includes('critical') || cardText.includes('hot')) {
                return 'alta';
            }
            
            if (cardText.includes('opcional') || cardText.includes('nice to have') ||
                cardText.includes('future') || cardText.includes('maybe') ||
                cardText.includes('low priority')) {
                return 'baixa';
            }

            return 'media'; // Prioridade padrão
        }
        
        async function importProjectFromJson(projectData) {
            console.log('🔄 Importando projeto:', projectData);
            
            try {
                // Mostrar progresso
                showProjectNotification(
                    '🔄 Importando projeto...',
                    `Processando ${projectData.tasks.length} tarefas`,
                    'info'
                );
                
                // Importar cada tarefa
                let importedCount = 0;
                let errorCount = 0;
                
                for (const task of projectData.tasks) {
                    try {
                        const response = await fetch('/api/atividades', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                titulo: task.title,
                                descricao: task.description || '',
                                status: task.status || 'pendente',
                                prioridade: task.priority || 'media',
                                data_limite: task.deadline || null,
                                categoria: projectData.name
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            importedCount++;
                        } else {
                            errorCount++;
                            console.error('Erro ao importar tarefa:', task.title, result);
                        }
                    } catch (error) {
                        errorCount++;
                        console.error('Erro na requisição:', error);
                    }
                }
                
                // Mostrar resultado final
                if (importedCount > 0) {
                    showProjectNotification(
                        `✅ Projeto "${projectData.name}" importado!`,
                        `${importedCount} atividades criadas${errorCount > 0 ? `, ${errorCount} com erro` : ''}`,
                        importedCount > errorCount ? 'success' : 'warning'
                    );
                } else {
                    showProjectNotification(
                        '❌ Erro na importação',
                        'Nenhuma atividade foi criada. Verifique os dados e tente novamente.',
                        'error'
                    );
                }
                
            } catch (error) {
                console.error('Erro na importação:', error);
                showProjectNotification(
                    '❌ Erro na importação',
                    'Erro interno do sistema. Tente novamente.',
                    'error'
                );
            }
        }
        
        function showProjectNotification(title, message, type) {
            // Remover notificações existentes
            const existingNotifications = document.querySelectorAll('.project-notification');
            existingNotifications.forEach(n => n.remove());
            
            // Criar notificação visual
            const notification = document.createElement('div');
            notification.className = `project-notification ${type}`;
            
            let borderColor = '#06b6d4'; // info
            if (type === 'success') borderColor = '#10b981';
            if (type === 'error') borderColor = '#ef4444';
            if (type === 'warning') borderColor = '#f59e0b';
            
            notification.innerHTML = `
                <div class="glass-card p-3" style="position: fixed; top: 100px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; border-left: 4px solid ${borderColor};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="glass-text mb-1">${title}</h6>
                            <p class="glass-text-muted small mb-0">${message}</p>
                        </div>
                        <button onclick="this.closest('.project-notification').remove()" class="btn btn-sm ms-2" style="background: none; border: none; color: rgba(255,255,255,0.6); font-size: 18px; line-height: 1; padding: 0;">&times;</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animação de entrada
            notification.style.animation = 'slideInRight 0.3s ease-out';
            
            // Remover após 8 segundos (mais tempo para ler)
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 8000);
        }
        
        // Exemplo de estrutura JSON esperada
        function showJsonExample() {
            const dashmeExample = {
                "name": "Meu Projeto de Desenvolvimento",
                "description": "Sistema completo de gerenciamento de tarefas",
                "tasks": [
                    {
                        "title": "Implementar autenticação",
                        "description": "Sistema de login e registro de usuários",
                        "priority": "alta",
                        "status": "pendente",
                        "deadline": "2025-08-15"
                    },
                    {
                        "title": "Criar dashboard principal", 
                        "description": "Interface principal com estatísticas",
                        "priority": "media",
                        "status": "em_andamento",
                        "deadline": "2025-08-20"
                    }
                ]
            };
            
            console.log('📋 Formato DashMEBoard:', JSON.stringify(dashmeExample, null, 2));
            console.log('🎯 TRELLO: Para importar do Trello:');
            console.log('1. Abra seu board no Trello');
            console.log('2. Vá em Menu > Mais > Imprimir e Exportar > Exportar JSON');
            console.log('3. Baixe o arquivo e faça upload aqui');
            console.log('4. O sistema detectará automaticamente e converterá os dados!');
            console.log('');
            console.log('✅ Mapeamento dos dados:');
            console.log('- Listas com "To Do", "Pendente", "Backlog" → Status: pendente');
            console.log('- Listas com "Doing", "Em Andamento", "Progress" → Status: em_andamento');
            console.log('- Listas com "Done", "Concluído", "Finished" → Status: concluída');
            console.log('- Labels com "High", "Alta", "Urgent" → Prioridade: alta');
            console.log('- Labels com "Medium", "Media", "Normal" → Prioridade: media');
            console.log('- Labels com "Low", "Baixa", "Opcional" → Prioridade: baixa');
            
            // Criar notificação com exemplo
            showProjectNotification(
                '📋 Formatos Suportados',
                '🎯 TRELLO: Exporte JSON do seu board | 📝 DASHME: Formato interno (veja console F12)',
                'info'
            );
        }
        
        // Função para baixar exemplo de JSON
        function downloadJsonExample() {
            const example = {
                "name": "Projeto Exemplo",
                "description": "Demonstração de estrutura JSON para import",
                "tasks": [
                    {
                        "title": "Configurar ambiente",
                        "description": "Instalar dependências e configurar projeto",
                        "priority": "alta",
                        "status": "pendente",
                        "deadline": "2025-08-10"
                    },
                    {
                        "title": "Desenvolver funcionalidades",
                        "description": "Implementar as principais features",
                        "priority": "media",
                        "status": "pendente"
                    }
                ]
            };
            
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(example, null, 2));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "exemplo-projeto.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
            
            showProjectNotification(
                '📥 Exemplo baixado!',
                'Arquivo "exemplo-projeto.json" foi baixado. Use-o como modelo.',
                'success'
            );
        }
        
        // Inicialização do DashMEBoard
        function initNeonDashboard() {
            console.log('✨ DashMEBoard inicializado com sucesso!');
            console.log('🎨 Background abstrato carregado');
            console.log('⏰ Sistema de relógio ativado');
            console.log('📁 Sistema de projetos com upload JSON ativado');
        }
        
        // Inicializar o dashboard
        initNeonDashboard();
        
        // Função para abrir o menu fullscreen
        function openFullscreenMenu() {
            const menu = document.getElementById('fullscreenMenu');
            if (menu) {
                menu.classList.add('show');
                // Resetar animações das seções
                const sections = menu.querySelectorAll('.menu-section');
                sections.forEach((section, index) => {
                    section.style.animation = 'none';
                    section.offsetHeight; // Trigger reflow
                    section.style.animation = `slideInFromTop 0.6s ease-out forwards`;
                    section.style.animationDelay = `${0.1 + (index * 0.1)}s`;
                });
            }
        }
        
        // Função para fechar o menu fullscreen
        function closeFullscreenMenu() {
            const menu = document.getElementById('fullscreenMenu');
            if (menu) {
                menu.classList.remove('show');
            }
        }
        
        // Função para navegar para seção e fechar menu
        function navigateToSection(sectionId) {
            closeDropdownMenu();
            setTimeout(() => {
                const target = document.getElementById(sectionId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 200); // Delay para aguardar o menu fechar
        }
        
        // Fechar dropdown ao pressionar ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDropdownMenu();
                closeFullscreenMenu(); // Manter para compatibilidade
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
        
        // Fechar menu fullscreen ao clicar no fundo (manter para compatibilidade)
        const fullscreenMenu = document.getElementById('fullscreenMenu');
        if (fullscreenMenu) {
            fullscreenMenu.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeFullscreenMenu();
                }
            });
        }
        
        // Navegação suave para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href.startsWith('#') && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        // Sistema do Biscoito da Sorte (Uma vez por dia)
        function initFortuneCookie() {
            const today = new Date().toDateString();
            const lastCookieDate = localStorage.getItem('lastFortuneCookieDate');
            const fortuneCookieSection = document.getElementById('fortuneCookieSection');
            const breakCookieBtn = document.getElementById('breakCookieBtn');
            const fortuneMessagePreview = document.getElementById('fortuneMessagePreview');
            const previewMessage = document.getElementById('previewMessage');
            const goToProfileBtn = document.getElementById('goToProfileBtn');

            // Mensagens de biscoito da sorte
            const fortuneMessages = [
                'O código que você escreve hoje será a base do futuro de amanhã. Continue programando com paixão! 💻✨',
                'A criatividade é infinita. Deixe sua imaginação voar e transforme ideias em realidade! 🎨🚀',
                'O sucesso não é acidente. É resultado de trabalho duro, persistência e determinação. Continue firme! 💪🔥',
                'A mente é como um jardim. Plante pensamentos positivos e colha felicidade! 🌱🧠',
                'A música é a linguagem universal da alma. Deixe suas emoções fluírem através das notas! 🎼🎵',
                'A paz interior é o maior tesouro. Respire fundo e encontre sua serenidade! 🧘‍♀️🌿',
                'Os dados não mentem. Use-os com sabedoria para criar um futuro melhor! 📈🧠',
                'A arte é a expressão mais pura da alma. Deixe sua criatividade brilhar! ✨🎨',
                'Seu corpo pode fazer qualquer coisa. É sua mente que você precisa convencer! 💪🔥',
                'A autenticidade é sua maior força. Seja você mesmo e inspire outros! 🌟📸',
                'A tecnologia é a ponte entre o sonho e a realidade. Continue construindo! 🌉💡',
                'Cada linha de código é uma oportunidade de criar algo incrível. Não desista! 🚀💻',
                'A inovação nasce da curiosidade. Continue explorando e descobrindo! 🔍💭',
                'O conhecimento é o investimento que sempre retorna dividendos. Continue aprendendo! 📚🎓',
                'A colaboração multiplica o sucesso. Conecte-se e cresça junto! 🤝🌟'
            ];

            // Mostrar biscoito apenas se não foi quebrado hoje
            if (lastCookieDate !== today) {
                fortuneCookieSection.style.display = 'block';
                
                // Animação de entrada
                fortuneCookieSection.style.opacity = '0';
                fortuneCookieSection.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    fortuneCookieSection.style.transition = 'all 0.5s ease';
                    fortuneCookieSection.style.opacity = '1';
                    fortuneCookieSection.style.transform = 'translateY(0)';
                }, 100);
            }

            // Evento de quebrar o biscoito
            if (breakCookieBtn) {
                breakCookieBtn.addEventListener('click', function() {
                    // Animação de quebra
                    this.style.transform = 'scale(0.8) rotate(5deg)';
                    this.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        // Selecionar mensagem aleatória
                        const randomMessage = fortuneMessages[Math.floor(Math.random() * fortuneMessages.length)];
                        
                        // Salvar mensagem no localStorage para usar no perfil
                        localStorage.setItem('todayFortuneMessage', randomMessage);
                        localStorage.setItem('lastFortuneCookieDate', today);
                        
                        // Mostrar mensagem
                        previewMessage.textContent = randomMessage;
                        fortuneMessagePreview.style.display = 'block';
                        fortuneMessagePreview.style.opacity = '0';
                        fortuneMessagePreview.style.transform = 'translateY(20px)';
                        
                        setTimeout(() => {
                            fortuneMessagePreview.style.transition = 'all 0.5s ease';
                            fortuneMessagePreview.style.opacity = '1';
                            fortuneMessagePreview.style.transform = 'translateY(0)';
                        }, 100);
                        
                        // Esconder botão de quebrar e mostrar botão de ir ao perfil
                        this.style.display = 'none';
                        goToProfileBtn.style.display = 'inline-block';
                        
                        // Esconder seção após 5 segundos
                        setTimeout(() => {
                            fortuneCookieSection.style.transition = 'all 0.5s ease';
                            fortuneCookieSection.style.opacity = '0';
                            fortuneCookieSection.style.transform = 'translateY(-20px)';
                            setTimeout(() => {
                                fortuneCookieSection.style.display = 'none';
                            }, 500);
                        }, 5000);
                        
                    }, 300);
                });
            }
        }

        // Inicializar biscoito da sorte
        initFortuneCookie();
    </script>
@endsection 
@extends('layouts.app')

@section('title', 'Perfil - DashMEBoard Neon')

@section('content')

    <!-- Conteúdo Principal -->
    <div class="container my-4">
        <div class="main-content-card">
            <!-- Header do Perfil -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title mb-4">
                        <div class="section-icon">👤</div>
                        <h2 class="glass-text mb-0">Meu Perfil</h2>
                        <p class="glass-text-muted">Estatísticas e desempenho dos últimos 30 dias</p>
                    </div>
                </div>
            </div>

            <!-- Cards de Estatísticas Principais -->
            <div class="row mb-4 g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card p-4 text-center">
                        <div class="stats-icon mb-3">
                            <i class="fas fa-tasks fa-3x" style="color: #06b6d4; opacity: 0.8;"></i>
                        </div>
                        <h3 class="glass-text mb-1">{{ $totalAtividades }}</h3>
                        <p class="glass-text-muted small mb-0">Total de Atividades</p>
                        <small class="glass-text-muted">Últimos 30 dias</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card p-4 text-center">
                        <div class="stats-icon mb-3">
                            <i class="fas fa-check-circle fa-3x" style="color: #10b981; opacity: 0.8;"></i>
                        </div>
                        <h3 class="glass-text mb-1">{{ $atividadesConcluidas }}</h3>
                        <p class="glass-text-muted small mb-0">Concluídas</p>
                        <small class="glass-text-muted">{{ $totalAtividades > 0 ? round(($atividadesConcluidas / $totalAtividades) * 100) : 0 }}% do total</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card p-4 text-center">
                        <div class="stats-icon mb-3">
                            <i class="fas fa-clock fa-3x" style="color: #f59e0b; opacity: 0.8;"></i>
                        </div>
                        <h3 class="glass-text mb-1">{{ $atividadesNoPrazo }}</h3>
                        <p class="glass-text-muted small mb-0">No Prazo</p>
                        <small class="glass-text-muted">{{ $atividadesConcluidas > 0 ? round(($atividadesNoPrazo / $atividadesConcluidas) * 100) : 0 }}% das concluídas</small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card p-4 text-center">
                        <div class="stats-icon mb-3">
                            <i class="fas fa-calendar-alt fa-3x" style="color: #8b5cf6; opacity: 0.8;"></i>
                        </div>
                        <h3 class="glass-text mb-1">30</h3>
                        <p class="glass-text-muted small mb-0">Dias de Histórico</p>
                        <small class="glass-text-muted">Período atual</small>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4 g-4">
                <!-- Gráfico de Prioridades -->
                <div class="col-lg-6">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-4">
                            <i class="fas fa-chart-pie me-2" style="color: #ef4444; opacity: 0.8;"></i>
                            Distribuição por Prioridade
                        </h5>
                        <div class="chart-container">
                            <canvas id="prioridadeChart"></canvas>
                        </div>
                        <div class="chart-legend mt-3">
                            <div class="d-flex justify-content-around">
                                <div class="text-center">
                                    <div class="legend-color" style="background: #ef4444;"></div>
                                    <small class="glass-text-muted">Alta ({{ $prioridadeAlta }})</small>
                                </div>
                                <div class="text-center">
                                    <div class="legend-color" style="background: #f59e0b;"></div>
                                    <small class="glass-text-muted">Média ({{ $prioridadeMedia }})</small>
                                </div>
                                <div class="text-center">
                                    <div class="legend-color" style="background: #06b6d4;"></div>
                                    <small class="glass-text-muted">Baixa ({{ $prioridadeBaixa }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Status -->
                <div class="col-lg-6">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-4">
                            <i class="fas fa-chart-donut me-2" style="color: #10b981; opacity: 0.8;"></i>
                            Status das Atividades
                        </h5>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="chart-legend mt-3">
                            <div class="d-flex justify-content-around">
                                <div class="text-center">
                                    <div class="legend-color" style="background: #10b981;"></div>
                                    <small class="glass-text-muted">Concluída ({{ $statusConcluida }})</small>
                                </div>
                                <div class="text-center">
                                    <div class="legend-color" style="background: #f59e0b;"></div>
                                    <small class="glass-text-muted">Em Andamento ({{ $statusEmAndamento }})</small>
                                </div>
                                <div class="text-center">
                                    <div class="legend-color" style="background: #6b7280;"></div>
                                    <small class="glass-text-muted">Pendente ({{ $statusPendente }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Atividades por Dia -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-4">
                            <i class="fas fa-chart-line me-2" style="color: #06b6d4; opacity: 0.8;"></i>
                            Atividades Criadas (Últimos 7 dias)
                        </h5>
                        <div class="chart-container">
                            <canvas id="atividadesPorDiaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Usuário -->
            <div class="row">
                <div class="col-12">
                    <div class="glass-card p-4">
                        <h5 class="glass-text mb-4">
                            <i class="fas fa-user-edit me-2" style="color: #8b5cf6; opacity: 0.8;"></i>
                            Informações Pessoais
                        </h5>
                        <form method="POST" action="{{ route('profile') }}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label glass-text">Nome Completo</label>
                                    <input type="text" class="form-control glass-input" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label glass-text">E-mail</label>
                                    <input type="email" class="form-control glass-input" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn glass-button">
                                            <i class="fas fa-save me-2"></i>Atualizar Perfil
                                        </button>
                                        <a href="{{ route('dashboard') }}" class="btn glass-button-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
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

        // Configuração dos gráficos
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        };

        // Gráfico de Prioridades (Doughnut)
        const prioridadeCtx = document.getElementById('prioridadeChart').getContext('2d');
        new Chart(prioridadeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Alta', 'Média', 'Baixa'],
                datasets: [{
                    data: [{{ $prioridadeAlta }}, {{ $prioridadeMedia }}, {{ $prioridadeBaixa }}],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)', 
                        'rgba(6, 182, 212, 0.8)'
                    ],
                    borderColor: [
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(6, 182, 212, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                ...chartOptions,
                cutout: '60%'
            }
        });

        // Gráfico de Status (Pie)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Concluída', 'Em Andamento', 'Pendente'],
                datasets: [{
                    data: [{{ $statusConcluida }}, {{ $statusEmAndamento }}, {{ $statusPendente }}],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(107, 114, 128, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: chartOptions
        });

        // Gráfico de Atividades por Dia (Line)
        const atividadesPorDiaCtx = document.getElementById('atividadesPorDiaChart').getContext('2d');
        const diasData = @json($atividadesPorDia);
        
        new Chart(atividadesPorDiaCtx, {
            type: 'line',
            data: {
                labels: diasData.map(item => item.data),
                datasets: [{
                    label: 'Atividades Criadas',
                    data: diasData.map(item => item.count),
                    borderColor: 'rgba(6, 182, 212, 1)',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(6, 182, 212, 1)',
                    pointBorderColor: 'rgba(255, 255, 255, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        }
                    }
                }
            }
        });

        console.log('✨ Página de Perfil carregada com sucesso!');
    </script>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    // Configuração dos gráficos
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // Gráfico de Prioridades (Doughnut)
    const prioridadeCtx = document.getElementById('prioridadeChart').getContext('2d');
    new Chart(prioridadeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Alta', 'Média', 'Baixa'],
            datasets: [{
                data: [{{ $prioridadeAlta }}, {{ $prioridadeMedia }}, {{ $prioridadeBaixa }}],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)', 
                    'rgba(6, 182, 212, 0.8)'
                ],
                borderColor: [
                    'rgba(239, 68, 68, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(6, 182, 212, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            ...chartOptions,
            cutout: '60%'
        }
    });

    // Gráfico de Status (Pie)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Concluída', 'Em Andamento', 'Pendente'],
            datasets: [{
                data: [{{ $statusConcluida }}, {{ $statusEmAndamento }}, {{ $statusPendente }}],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(107, 114, 128, 0.8)'
                ],
                borderColor: [
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(107, 114, 128, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: chartOptions
    });

    // Gráfico de Atividades por Dia (Line)
    const atividadesPorDiaCtx = document.getElementById('atividadesPorDiaChart').getContext('2d');
    const diasData = @json($atividadesPorDia);
    
    new Chart(atividadesPorDiaCtx, {
        type: 'line',
        data: {
            labels: diasData.map(item => item.data),
            datasets: [{
                label: 'Atividades Criadas',
                data: diasData.map(item => item.count),
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(6, 182, 212, 1)',
                pointBorderColor: 'rgba(255, 255, 255, 1)',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                }
            }
        }
    });

    console.log('✨ Página de Perfil carregada com sucesso!');
</script>
@endsection 
@extends('layouts.app')

@section('content')
<div class="container my-4 glass-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Cabeçalho do Perfil -->
            <div class="glass-card p-4 mb-4">
                <div class="d-flex align-items-center mb-4">
                    @if($profile->profile_image)
                        <img src="{{ $profile->profile_image_url }}" 
                             class="rounded-circle me-4" 
                             width="100" height="100" 
                             alt="{{ $profile->user->name }}">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-4" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-white fa-3x"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <h2 class="glass-text mb-1">{{ $profile->user->name }}</h2>
                        <p class="glass-text-muted mb-2">@{{ $profile->username }}</p>
                        @if($profile->profession)
                            <span class="glass-badge">
                                <i class="fas fa-briefcase me-1"></i>{{ $profile->profession }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('profiles.edit') }}" class="btn glass-button">
                            <i class="fas fa-edit me-2"></i>Editar Perfil
                        </a>
                        <a href="{{ route('profiles.index') }}" class="btn glass-button-secondary">
                            <i class="fas fa-users me-2"></i>Ver Outros
                        </a>
                    </div>
                </div>

                <!-- Status do Dia -->
                <div class="row mb-4">
                    @if($profile->mood)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-2">
                                <i class="fas fa-smile me-2"></i>Humor do dia
                            </h6>
                            <p class="glass-text-muted mb-0">{{ $profile->mood }}</p>
                        </div>
                    </div>
                    @endif

                    @if($profile->daily_music)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-2">
                                <i class="fas fa-music me-2"></i>Música do dia
                            </h6>
                            <p class="glass-text-muted mb-0">{{ $profile->daily_music }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Bio -->
                @if($profile->bio)
                <div class="glass-card p-3 mb-4">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-quote-left me-2"></i>Sobre você
                    </h5>
                    <p class="glass-text-muted mb-0">{{ $profile->bio }}</p>
                </div>
                @endif

                <!-- Biscoito da Sorte do Dia -->
                <div class="glass-card p-4 mb-4" id="fortuneCookieCard" style="background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 193, 7, 0.1)); border: 2px solid rgba(255, 193, 7, 0.3);">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-cookie-bite fa-3x" style="color: #FFD700;"></i>
                        </div>
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-star me-2" style="color: #FFD700;"></i>Biscoito da Sorte do Dia
                        </h5>
                        <div class="fortune-cookie-message p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px; border-left: 4px solid #FFD700;">
                            <p class="glass-text mb-0" id="fortuneMessageText" style="font-style: italic;">
                                @if($profile->fortune_cookie_message)
                                    "{{ $profile->fortune_cookie_message }}"
                                @else
                                    Quebre o biscoito no dashboard para receber sua mensagem do dia!
                                @endif
                            </p>
                        </div>
                        <small class="glass-text-muted mt-2 d-block">✨ Sua mensagem inspiradora do dia ✨</small>
                    </div>
                </div>

                <!-- Plano de Fundo Personalizado -->
                <div class="glass-card p-4 mb-4">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-image me-2"></i>Plano de Fundo do Perfil
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="background-option-select" data-bg="1">
                                <div class="background-preview-select">
                                    <img src="https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146672/662902ceb3ffcae10a826e3250ff7c4e_ox9tyq.jpg" class="img-fluid rounded shadow-sm" alt="Fundo 1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="background-option-select" data-bg="2">
                                <div class="background-preview-select">
                                    <img src="https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146615/assets_task_01k0qsyhx4fbjbq7k8z19bxkw2_1753145794_img_0_aza4ph.webp" class="img-fluid rounded shadow-sm" alt="Fundo 2">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="background-option-select" data-bg="3">
                                <div class="background-preview-select">
                                    <img src="https://res.cloudinary.com/dzwfuzxxw/image/upload/v1752606826/20250703_1317_Imagem_Abstrata_Neon_remix_01jz8h2my8e48rmp8ka3s961d6_potf77.png" class="img-fluid rounded shadow-sm" alt="Fundo 3">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="glass-text-muted">O plano de fundo escolhido permanecerá ativo em todo o sistema até você escolher outro.</small>
                    </div>
                </div>

                <style>
                .background-option-select {
                    border: 2px solid transparent;
                    border-radius: 12px;
                    cursor: pointer;
                    transition: border 0.3s, box-shadow 0.3s;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                }
                .background-option-select.selected {
                    border: 2px solid #00d4ff;
                    box-shadow: 0 0 16px #00d4ff44;
                }
                .background-preview-select img {
                    width: 100%;
                    height: 120px;
                    object-fit: cover;
                    border-radius: 10px;
                }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // URLs dos fundos
                    const backgrounds = [
                        '', // índice 0 não usado
                        'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146672/662902ceb3ffcae10a826e3250ff7c4e_ox9tyq.jpg',
                        'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1753146615/assets_task_01k0qsyhx4fbjbq7k8z19bxkw2_1753145794_img_0_aza4ph.webp',
                        'https://res.cloudinary.com/dzwfuzxxw/image/upload/v1752606826/20250703_1317_Imagem_Abstrata_Neon_remix_01jz8h2my8e48rmp8ka3s961d6_potf77.png'
                    ];
                    
                    // Carregar seleção do banco de dados ou localStorage
                    let selected = '1'; // padrão
                    
                    // Verificar se há plano de fundo salvo no perfil
                    @if($profile->background_image_url)
                        const profileBackground = '{{ $profile->background_image_url }}';
                        const backgroundIndex = backgrounds.indexOf(profileBackground);
                        if (backgroundIndex > 0) {
                            selected = backgroundIndex.toString();
                            localStorage.setItem('userBackgroundImage', profileBackground);
                            localStorage.setItem('userBackgroundImageIndex', selected);
                        }
                    @else
                        // Usar localStorage se não há no banco
                        selected = localStorage.getItem('userBackgroundImageIndex') || '1';
                    @endif
                    
                    applyBackground(selected);
                    highlightSelected(selected);

                    // Clique para selecionar
                    document.querySelectorAll('.background-option-select').forEach(opt => {
                        opt.addEventListener('click', function() {
                            const idx = this.getAttribute('data-bg');
                            const selectedUrl = backgrounds[idx];
                            
                            // Salvar no localStorage
                            localStorage.setItem('userBackgroundImage', selectedUrl);
                            localStorage.setItem('userBackgroundImageIndex', idx);
                            
                            // Salvar no banco de dados
                            fetch('/profiles/update-background', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    background_image_url: selectedUrl
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Plano de fundo salvo no banco de dados');
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao salvar plano de fundo:', error);
                            });
                            
                            applyBackground(idx);
                            highlightSelected(idx);
                        });
                    });

                    function applyBackground(idx) {
                        if (backgrounds[idx]) {
                            document.body.style.backgroundImage = `url(${backgrounds[idx]})`;
                            document.body.style.backgroundSize = 'cover';
                            document.body.style.backgroundPosition = 'center';
                            document.body.style.backgroundAttachment = 'fixed';
                        }
                    }
                    function highlightSelected(idx) {
                        document.querySelectorAll('.background-option-select').forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        const selectedOpt = document.querySelector('.background-option-select[data-bg="'+idx+'"]');
                        if (selectedOpt) selectedOpt.classList.add('selected');
                    }
                });
                </script>

                <!-- Agendas -->
                <div class="row mb-4">
                    @if($profile->public_agenda)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3">
                            <h5 class="glass-text mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Agenda Pública
                            </h5>
                            <p class="glass-text-muted mb-0">{{ $profile->public_agenda }}</p>
                        </div>
                    </div>
                    @endif

                    @if($profile->private_agenda)
                    <div class="col-md-6 mb-3">
                        <div class="glass-card p-3" style="background: rgba(255, 193, 7, 0.1);">
                            <h5 class="glass-text mb-3">
                                <i class="fas fa-lock me-2"></i>Agenda Privada
                            </h5>
                            <p class="glass-text-muted mb-0">{{ $profile->private_agenda }}</p>
                            <small class="glass-text-muted">Só você pode ver isso</small>
                        </div>
                    </div>
                    @endif
                </div>



                <!-- Configurações -->
                <div class="glass-card p-3">
                    <h5 class="glass-text mb-3">
                        <i class="fas fa-cog me-2"></i>Configurações
                    </h5>
                    <div class="d-flex align-items-center">
                        @if($profile->is_public)
                            <span class="glass-badge me-3" style="background: rgba(40, 167, 69, 0.2); color: #28a745;">
                                <i class="fas fa-globe me-1"></i>Perfil Público
                            </span>
                        @else
                            <span class="glass-badge me-3" style="background: rgba(108, 117, 125, 0.2); color: #6c757d;">
                                <i class="fas fa-lock me-1"></i>Perfil Privado
                            </span>
                        @endif
                        <small class="glass-text-muted">
                            @if($profile->is_public)
                                Seu perfil está visível para todos
                            @else
                                Seu perfil está privado
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Perfil -->
            <div class="glass-card p-4 mb-4">
                <h4 class="glass-text mb-4 text-center">
                    <i class="fas fa-chart-bar me-2"></i>Minhas Estatísticas (Últimos 30 dias)
                </h4>
                
                <!-- Cards de Estatísticas Principais -->
                <div class="row mb-4 g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-card p-3 text-center">
                            <div class="stats-icon mb-2">
                                <i class="fas fa-tasks fa-2x" style="color: #06b6d4; opacity: 0.8;"></i>
                            </div>
                            <h3 class="glass-text mb-1">{{ $atividadesCount }}</h3>
                            <p class="glass-text-muted small mb-0">Total de Atividades</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-card p-3 text-center">
                            <div class="stats-icon mb-2">
                                <i class="fas fa-check-circle fa-2x" style="color: #10b981; opacity: 0.8;"></i>
                            </div>
                            <h3 class="glass-text mb-1">{{ $atividadesConcluidas }}</h3>
                            <p class="glass-text-muted small mb-0">Concluídas</p>
                            <small class="glass-text-muted">{{ $atividadesCount > 0 ? round(($atividadesConcluidas / $atividadesCount) * 100) : 0 }}% do total</small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-card p-3 text-center">
                            <div class="stats-icon mb-2">
                                <i class="fas fa-clock fa-2x" style="color: #f59e0b; opacity: 0.8;"></i>
                            </div>
                            <h3 class="glass-text mb-1">{{ $atividadesNoPrazo }}</h3>
                            <p class="glass-text-muted small mb-0">No Prazo</p>
                            <small class="glass-text-muted">{{ $atividadesConcluidas > 0 ? round(($atividadesNoPrazo / $atividadesConcluidas) * 100) : 0 }}% das concluídas</small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-card p-3 text-center">
                            <div class="stats-icon mb-2">
                                <i class="fas fa-calendar-alt fa-2x" style="color: #8b5cf6; opacity: 0.8;"></i>
                            </div>
                            <h3 class="glass-text mb-1">30</h3>
                            <p class="glass-text-muted small mb-0">Dias de Histórico</p>
                            <small class="glass-text-muted">Período atual</small>
                        </div>
                    </div>
                </div>

                <!-- Gráficos de Prioridade e Status -->
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-3">
                                <i class="fas fa-chart-pie me-2" style="color: #ef4444; opacity: 0.8;"></i>
                                Distribuição por Prioridade
                            </h6>
                            <div class="d-flex justify-content-around text-center">
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">
                                        Alta ({{ $prioridadeAlta }})
                                    </div>
                                </div>
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                                        Média ({{ $prioridadeMedia }})
                                    </div>
                                </div>
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(6, 182, 212, 0.2); color: #06b6d4;">
                                        Baixa ({{ $prioridadeBaixa }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="glass-card p-3">
                            <h6 class="glass-text mb-3">
                                <i class="fas fa-chart-donut me-2" style="color: #10b981; opacity: 0.8;"></i>
                                Status das Atividades
                            </h6>
                            <div class="d-flex justify-content-around text-center">
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
                                        Concluída ({{ $statusConcluida }})
                                    </div>
                                </div>
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                                        Em Andamento ({{ $statusEmAndamento }})
                                    </div>
                                </div>
                                <div>
                                    <div class="glass-badge mb-1" style="background: rgba(107, 114, 128, 0.2); color: #6b7280;">
                                        Pendente ({{ $statusPendente }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links de Navegação -->
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('profiles.edit') }}" class="btn glass-button">
                    <i class="fas fa-edit me-2"></i>Editar Perfil
                </a>
                <a href="{{ route('profiles.index') }}" class="btn glass-button-secondary">
                    <i class="fas fa-users me-2"></i>Descobrir Perfis
                </a>
                <a href="{{ route('messages.index') }}" class="btn glass-button-secondary">
                    <i class="fas fa-envelope me-2"></i>Mensagens
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Carregar mensagem do biscoito da sorte do dia
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toDateString();
    const lastCookieDate = localStorage.getItem('lastFortuneCookieDate');
    const todayFortuneMessage = localStorage.getItem('todayFortuneMessage');
    const fortuneMessageText = document.getElementById('fortuneMessageText');
    const fortuneCookieCard = document.getElementById('fortuneCookieCard');

    // Se quebrou o biscoito hoje, mostrar a mensagem do dia
    if (lastCookieDate === today && todayFortuneMessage) {
        fortuneMessageText.textContent = `"${todayFortuneMessage}"`;
        
        // Adicionar animação especial
        fortuneCookieCard.style.animation = 'cookieGlow 2s ease-in-out infinite alternate';
    }
});

// Animação de brilho para o biscoito do dia
const style = document.createElement('style');
style.textContent = `
    @keyframes cookieGlow {
        from {
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            border-color: rgba(255, 215, 0, 0.5);
        }
        to {
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
            border-color: rgba(255, 215, 0, 0.8);
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection 
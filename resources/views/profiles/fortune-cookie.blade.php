@extends('layouts.app')

@section('title', 'Biscoito da Sorte - DashMEBoard Neon')

@section('content')
<div class="glass-container">
    <div class="main-content-card">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="glass-text mb-2">
                    <i class="fas fa-cookie-bite me-2"></i>Biscoito da Sorte
                </h1>
                <p class="glass-text-muted mb-0">Quebre o biscoito e escolha sua imagem de fundo personalizada!</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('profiles.my-profile') }}" class="btn btn-glass-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Perfil
                </a>
            </div>
        </div>

        <!-- Biscoito da Sorte -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="fortune-cookie-container text-center">
                    <div class="fortune-cookie" id="fortuneCookie">
                        <div class="cookie-icon">
                            <i class="fas fa-cookie-bite fa-4x"></i>
                        </div>
                        <h3 class="glass-text mt-3">Clique para quebrar o biscoito!</h3>
                        <p class="glass-text-muted">Descubra sua mensagem da sorte</p>
                    </div>
                    
                    <div class="fortune-message" id="fortuneMessage" style="display: none;">
                        <div class="message-card">
                            <i class="fas fa-quote-left fa-2x glass-text-muted mb-3"></i>
                            <p class="fortune-text" id="fortuneText"></p>
                            <i class="fas fa-quote-right fa-2x glass-text-muted mt-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seleção de Imagem de Fundo -->
        <div class="background-selection" id="backgroundSelection" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <h3 class="glass-text mb-4">
                        <i class="fas fa-image me-2"></i>Escolha sua imagem de fundo
                    </h3>
                    <p class="glass-text-muted mb-4">A imagem escolhida será exibida apenas quando você estiver visualizando seu perfil</p>
                </div>
            </div>
            
            <div class="row">
                @foreach($backgroundImages as $index => $imageUrl)
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="background-option" data-image-url="{{ $imageUrl }}">
                        <div class="background-preview">
                            <img src="{{ $imageUrl }}" alt="Imagem de fundo {{ $index + 1 }}" class="img-fluid">
                            <div class="background-overlay">
                                <button class="btn btn-glass select-background-btn">
                                    <i class="fas fa-check me-2"></i>Selecionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Preview da Imagem Selecionada -->
        <div class="background-preview-section" id="backgroundPreviewSection" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <h4 class="glass-text mb-3">
                        <i class="fas fa-eye me-2"></i>Preview da sua imagem de fundo
                    </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="profile-preview-card">
                        <div class="preview-background" id="previewBackground">
                            <!-- A imagem será aplicada via CSS -->
                        </div>
                        <div class="preview-content">
                            <div class="preview-avatar">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <h5 class="glass-text mt-3">{{ $profile->username ?? 'Seu Username' }}</h5>
                            <p class="glass-text-muted">{{ $profile->profession ?? 'Sua Profissão' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button class="btn btn-glass-success me-3" id="confirmBackground">
                        <i class="fas fa-check me-2"></i>Confirmar Imagem
                    </button>
                    <button class="btn btn-glass-secondary" id="changeBackground">
                        <i class="fas fa-undo me-2"></i>Escolher Outra
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-content">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="glass-text mt-3">Atualizando sua imagem de fundo...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedBackgroundUrl = null;
let selectedFortuneMessage = null;

document.addEventListener('DOMContentLoaded', function() {
    const fortuneCookie = document.getElementById('fortuneCookie');
    const fortuneMessage = document.getElementById('fortuneMessage');
    const fortuneText = document.getElementById('fortuneText');
    const backgroundSelection = document.getElementById('backgroundSelection');
    const backgroundPreviewSection = document.getElementById('backgroundPreviewSection');
    const previewBackground = document.getElementById('previewBackground');
    const confirmBackground = document.getElementById('confirmBackground');
    const changeBackground = document.getElementById('changeBackground');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Mensagens de biscoito da sorte
    const fortuneMessages = @json($fortuneMessages);

    // Quebrar o biscoito
    fortuneCookie.addEventListener('click', function() {
        // Animação de quebra
        fortuneCookie.style.transform = 'scale(0.8) rotate(5deg)';
        fortuneCookie.style.opacity = '0.5';
        
        setTimeout(() => {
            // Selecionar mensagem aleatória
            const randomMessage = fortuneMessages[Math.floor(Math.random() * fortuneMessages.length)];
            selectedFortuneMessage = randomMessage;
            
            // Mostrar mensagem
            fortuneText.textContent = randomMessage;
            fortuneCookie.style.display = 'none';
            fortuneMessage.style.display = 'block';
            
            // Animação de entrada da mensagem
            fortuneMessage.style.opacity = '0';
            fortuneMessage.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                fortuneMessage.style.transition = 'all 0.5s ease';
                fortuneMessage.style.opacity = '1';
                fortuneMessage.style.transform = 'translateY(0)';
                
                // Mostrar seleção de imagem após 2 segundos
                setTimeout(() => {
                    backgroundSelection.style.display = 'block';
                    backgroundSelection.style.opacity = '0';
                    backgroundSelection.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        backgroundSelection.style.transition = 'all 0.5s ease';
                        backgroundSelection.style.opacity = '1';
                        backgroundSelection.style.transform = 'translateY(0)';
                    }, 100);
                }, 2000);
            }, 100);
        }, 300);
    });

    // Seleção de imagem de fundo
    document.querySelectorAll('.background-option').forEach(option => {
        option.addEventListener('click', function() {
            const imageUrl = this.dataset.imageUrl;
            selectedBackgroundUrl = imageUrl;
            
            // Remover seleção anterior
            document.querySelectorAll('.background-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Marcar como selecionada
            this.classList.add('selected');
            
            // Mostrar preview
            previewBackground.style.backgroundImage = `url(${imageUrl})`;
            backgroundPreviewSection.style.display = 'block';
            backgroundPreviewSection.style.opacity = '0';
            backgroundPreviewSection.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                backgroundPreviewSection.style.transition = 'all 0.5s ease';
                backgroundPreviewSection.style.opacity = '1';
                backgroundPreviewSection.style.transform = 'translateY(0)';
            }, 100);
        });
    });

    // Confirmar imagem de fundo
    confirmBackground.addEventListener('click', function() {
        if (!selectedBackgroundUrl) {
            alert('Por favor, selecione uma imagem de fundo primeiro.');
            return;
        }

        loadingOverlay.style.display = 'flex';
        
        fetch('{{ route("update-background") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                background_image_url: selectedBackgroundUrl,
                fortune_message: selectedFortuneMessage
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.style.display = 'none';
            
            if (data.success) {
                // Salvar no localStorage para persistir durante a sessão do perfil
                localStorage.setItem('userBackgroundImage', selectedBackgroundUrl);
                
                // Mostrar notificação de sucesso
                showNotification('Imagem de fundo atualizada com sucesso!', 'success');
                
                // Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = '{{ route("profiles.my-profile") }}';
                }, 2000);
            } else {
                showNotification('Erro ao atualizar imagem de fundo: ' + data.message, 'error');
            }
        })
        .catch(error => {
            loadingOverlay.style.display = 'none';
            console.error('Erro:', error);
            showNotification('Erro ao atualizar imagem de fundo', 'error');
        });
    });

    // Escolher outra imagem
    changeBackground.addEventListener('click', function() {
        backgroundPreviewSection.style.display = 'none';
        document.querySelectorAll('.background-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        selectedBackgroundUrl = null;
    });

    // Função para mostrar notificações
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} glass-card alert-dismissible fade show`;
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.main-content').insertBefore(notification, document.querySelector('.main-content').firstChild);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
@endsection 
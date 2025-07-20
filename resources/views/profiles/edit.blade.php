@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <h2 class="glass-text">
                        <i class="fas fa-user-edit me-2"></i>{{ $profile->id ? 'Editar' : 'Criar' }} Perfil
                    </h2>
                    <p class="glass-text-muted">Personalize seu perfil e compartilhe com o mundo!</p>
                </div>

                @if($errors->any())
                    <div class="glass-card p-3 mb-4" style="border-color: #ff6b6b; background: rgba(255, 107, 107, 0.1);">
                        @foreach($errors->all() as $error)
                            <div class="glass-text" style="color: #ff6b6b;">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Informações Básicas -->
                        <div class="col-md-6">
                            <div class="glass-card p-3 mb-3">
                                <h5 class="glass-text mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informações Básicas
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label glass-text">
                                        <i class="fas fa-at me-2"></i>Nome de usuário
                                    </label>
                                    <input type="text" class="form-control glass-input" id="username" name="username" 
                                           value="{{ old('username', $profile->username) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="profession" class="form-label glass-text">
                                        <i class="fas fa-briefcase me-2"></i>Profissão
                                    </label>
                                    <input type="text" class="form-control glass-input" id="profession" name="profession" 
                                           value="{{ old('profession', $profile->profession) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="mood" class="form-label glass-text">
                                        <i class="fas fa-smile me-2"></i>Humor do dia
                                    </label>
                                    <input type="text" class="form-control glass-input" id="mood" name="mood" 
                                           value="{{ old('mood', $profile->mood) }}" placeholder="Ex: Feliz, Motivado, Calmo...">
                                </div>

                                <div class="mb-3">
                                    <label for="daily_music" class="form-label glass-text">
                                        <i class="fas fa-music me-2"></i>Música do dia
                                    </label>
                                    <input type="text" class="form-control glass-input" id="daily_music" name="daily_music" 
                                           value="{{ old('daily_music', $profile->daily_music) }}" placeholder="Música que está ouvindo hoje">
                                </div>
                            </div>
                        </div>

                        <!-- Imagens -->
                        <div class="col-md-6">
                            <div class="glass-card p-3 mb-3">
                                <h5 class="glass-text mb-3">
                                    <i class="fas fa-images me-2"></i>Imagens
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label glass-text">
                                        <i class="fas fa-user-circle me-2"></i>Foto de perfil
                                    </label>
                                    <input type="file" class="form-control glass-input" id="profile_image" name="profile_image" 
                                           accept="image/*">
                                    @if($profile->profile_image)
                                        <small class="glass-text-muted">Imagem atual: {{ $profile->profile_image }}</small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="background_image" class="form-label glass-text">
                                        <i class="fas fa-image me-2"></i>Imagem de fundo
                                    </label>
                                    <input type="file" class="form-control glass-input" id="background_image" name="background_image" 
                                           accept="image/*">
                                    @if($profile->background_image)
                                        <small class="glass-text-muted">Imagem atual: {{ $profile->background_image }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="glass-card p-3 mb-3">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-quote-left me-2"></i>Sobre você
                        </h5>
                        <div class="mb-3">
                            <label for="bio" class="form-label glass-text">Bio</label>
                            <textarea class="form-control glass-input" id="bio" name="bio" rows="3" 
                                      placeholder="Conte um pouco sobre você...">{{ old('bio', $profile->bio) }}</textarea>
                        </div>
                    </div>

                    <!-- Agendas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="glass-card p-3 mb-3">
                                <h5 class="glass-text mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>Agenda Pública
                                </h5>
                                <div class="mb-3">
                                    <label for="public_agenda" class="form-label glass-text">O que você está fazendo hoje?</label>
                                    <textarea class="form-control glass-input" id="public_agenda" name="public_agenda" rows="3" 
                                              placeholder="Compartilhe seus planos públicos...">{{ old('public_agenda', $profile->public_agenda) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="glass-card p-3 mb-3">
                                <h5 class="glass-text mb-3">
                                    <i class="fas fa-lock me-2"></i>Agenda Privada
                                </h5>
                                <div class="mb-3">
                                    <label for="private_agenda" class="form-label glass-text">Notas pessoais (só você vê)</label>
                                    <textarea class="form-control glass-input" id="private_agenda" name="private_agenda" rows="3" 
                                              placeholder="Suas notas pessoais...">{{ old('private_agenda', $profile->private_agenda) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biscoito da Sorte -->
                    <div class="glass-card p-3 mb-3">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-cookie-bite me-2"></i>Biscoito da Sorte
                        </h5>
                        <div class="mb-3">
                            <label for="fortune_cookie_message" class="form-label glass-text">Mensagem personalizada</label>
                            <textarea class="form-control glass-input" id="fortune_cookie_message" name="fortune_cookie_message" rows="2" 
                                      placeholder="Uma mensagem inspiradora ou engraçada para quem visitar seu perfil...">{{ old('fortune_cookie_message', $profile->fortune_cookie_message) }}</textarea>
                            <small class="glass-text-muted">Esta mensagem aparecerá como um "biscoito da sorte" para visitantes</small>
                        </div>
                    </div>

                    <!-- Configurações -->
                    <div class="glass-card p-3 mb-4">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-cog me-2"></i>Configurações
                        </h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input glass-checkbox" id="is_public" name="is_public" 
                                   {{ old('is_public', $profile->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label glass-text" for="is_public">
                                <i class="fas fa-globe me-2"></i>Tornar perfil público
                            </label>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn glass-button flex-fill">
                            <i class="fas fa-save me-2"></i>{{ $profile->id ? 'Atualizar' : 'Criar' }} Perfil
                        </button>
                        <a href="{{ route('profiles.my-profile') }}" class="btn glass-button-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 
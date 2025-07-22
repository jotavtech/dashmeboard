@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <h2 class="glass-text">
                        <i class="fas fa-paper-plane me-2"></i>Nova Mensagem
                    </h2>
                    <p class="glass-text-muted">Envie uma mensagem para alguém especial</p>
                </div>

                @if($errors->any())
                    <div class="glass-card p-3 mb-4" style="border-color: #ff6b6b; background: rgba(255, 107, 107, 0.1);">
                        @foreach($errors->all() as $error)
                            <div class="glass-text" style="color: #ff6b6b;">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    
                    <!-- Destinatário -->
                    <div class="glass-card p-3 mb-4">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-user me-2"></i>Destinatário
                        </h5>
                        
                        @if($recipient)
                            <div class="d-flex align-items-center p-3" style="background: rgba(0, 123, 255, 0.1); border-radius: 8px;">
                                @if($recipient->profile && $recipient->profile->profile_image)
                                    <img src="{{ $recipient->profile->profile_image_url }}" 
                                         class="rounded-circle me-3" 
                                         width="50" height="50" 
                                         alt="{{ $recipient->name }}">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="glass-text mb-1">{{ $recipient->name }}</h6>
                                    @if($recipient->profile)
                                        <p class="glass-text-muted mb-0">@{{ $recipient->profile->username }}</p>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="to_user_id" value="{{ $recipient->id }}">
                        @else
                            <div class="mb-3">
                                <label for="to_user_id" class="form-label glass-text">Selecionar destinatário</label>
                                <select class="form-control glass-input" id="to_user_id" name="to_user_id" required>
                                    <option value="">Escolha uma pessoa...</option>
                                    @foreach(\App\Models\User::whereHas('profile', function($q) { $q->where('is_public', true); })->with('profile')->get() as $user)
                                        @if($user->id !== auth()->id())
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} 
                                                @if($user->profile)
                                                    (@{{ $user->profile->username }})
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Mensagem -->
                    <div class="glass-card p-3 mb-4">
                        <h5 class="glass-text mb-3">
                            <i class="fas fa-comment me-2"></i>Sua Mensagem
                        </h5>
                        <div class="mb-3">
                            <label for="message" class="form-label glass-text">Mensagem</label>
                            <textarea class="form-control glass-input" id="message" name="message" 
                                      rows="6" placeholder="Digite sua mensagem aqui..." required>{{ old('message') }}</textarea>
                            <small class="glass-text-muted">Máximo 1000 caracteres</small>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn glass-button flex-fill">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                        </button>
                        <a href="{{ route('messages.index') }}" class="btn glass-button-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 
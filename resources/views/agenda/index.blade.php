@extends('layouts.app')

@section('styles')
<style>
/* Estilos do Calendário */
.calendar-container {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.2),
        0 4px 16px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.calendar-title {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

.calendar-nav {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.calendar-nav-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.8);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.calendar-nav-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: rgba(255, 255, 255, 1);
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.weekday {
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 600;
    padding: 0.5rem;
    font-size: 0.9rem;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.calendar-day {
    aspect-ratio: 1;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 0.5rem;
    position: relative;
    transition: all 0.3s ease;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.05);
}

.calendar-day:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.calendar-day.other-month {
    opacity: 0.3;
    background: rgba(255, 255, 255, 0.02);
}

.calendar-day.today {
    background: rgba(0, 123, 255, 0.2);
    border-color: rgba(0, 123, 255, 0.5);
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
}

.calendar-day {
    position: relative;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 0.5rem;
}

.day-number {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.day-activities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    width: 100%;
    margin-top: auto;
}

.activity-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.activity-dot:hover {
    transform: scale(1.2);
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
}

.activity-more {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
    margin-left: 0.25rem;
}

.calendar-day.other-month .day-number {
    color: rgba(255, 255, 255, 0.3);
}

.calendar-day.other-month .activity-dot {
    opacity: 0.5;
}

.calendar-day:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.calendar-day.has-events {
    background: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.3);
}

.day-number {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    font-size: 0.9rem;
}

.day-events {
    position: absolute;
    bottom: 0.5rem;
    left: 0.5rem;
    right: 0.5rem;
}

.event-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 193, 7, 0.8);
    margin: 1px;
    display: inline-block;
}

.event-indicator.public {
    background: rgba(40, 167, 69, 0.8);
}

.event-indicator.private {
    background: rgba(108, 117, 125, 0.8);
}

/* Modal de Adicionar/Editar Evento */
.event-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 1050;
}

.event-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-modal-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 2rem;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.event-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.event-modal-title {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
}

.event-modal-close {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.event-modal-close:hover {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.form-check-input {
    width: 18px;
    height: 18px;
    accent-color: rgba(0, 123, 255, 0.8);
}

.form-check-label {
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
}

.btn-primary {
    background: rgba(0, 123, 255, 0.8);
    border: 1px solid rgba(0, 123, 255, 0.5);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-primary:hover {
    background: rgba(0, 123, 255, 1);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
}

.btn-secondary {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.8);
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: rgba(255, 255, 255, 1);
}

.btn-danger {
    background: rgba(220, 53, 69, 0.8);
    border: 1px solid rgba(220, 53, 69, 0.5);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-danger:hover {
    background: rgba(220, 53, 69, 1);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

/* Lista de Eventos */
.events-list {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 2rem;
}

.events-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.events-title {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
}

.event-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.event-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.event-title {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.event-actions {
    display: flex;
    gap: 0.5rem;
}

.event-action-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.7);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.event-action-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: rgba(255, 255, 255, 1);
}

.event-details {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.event-description {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
    margin: 0;
}

.event-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 0.5rem;
}

.event-badge.public {
    background: rgba(40, 167, 69, 0.2);
    color: rgba(40, 167, 69, 0.9);
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.event-badge.private {
    background: rgba(108, 117, 125, 0.2);
    color: rgba(108, 117, 125, 0.9);
    border: 1px solid rgba(108, 117, 125, 0.3);
}

/* Responsividade */
@media (max-width: 768px) {
    .calendar-container {
        padding: 1rem;
    }
    
    .calendar-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .calendar-nav {
        width: 100%;
        justify-content: center;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .event-modal-content {
        width: 95%;
        padding: 1.5rem;
    }
}

/* Notificações */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 1rem 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 9999;
    max-width: 400px;
}

.notification.show {
    transform: translateX(0);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #333;
}

.notification-success .notification-content i {
    color: #28a745;
}

.notification-error .notification-content i {
    color: #dc3545;
}

.notification-info .notification-content i {
    color: #17a2b8;
}

/* Animações */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* Atividades na lista */
.activity-item {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.activity-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.activity-title {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.activity-actions {
    display: flex;
    gap: 0.5rem;
}

.activity-actions .btn {
    border-radius: 6px;
    transition: all 0.3s ease;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

.activity-actions .btn-outline-light {
    color: rgba(255, 255, 255, 0.8);
    border-color: rgba(255, 255, 255, 0.3);
}

.activity-actions .btn-outline-light:hover {
    color: #333;
    background-color: rgba(255, 255, 255, 0.9);
    border-color: rgba(255, 255, 255, 0.5);
}

.activity-actions .btn-outline-danger {
    color: rgba(220, 53, 69, 0.8);
    border-color: rgba(220, 53, 69, 0.3);
}

.activity-actions .btn-outline-danger:hover {
    color: #fff;
    background-color: rgba(220, 53, 69, 0.8);
    border-color: rgba(220, 53, 69, 0.5);
}

.activity-description {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
}

.activity-date, .activity-time {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.activity-status {
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.status-completed {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
}

.status-cancelled {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

/* Modal de Atividades do Dia */
.day-activities-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 1060;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.day-activities-modal.show {
    opacity: 1;
}

.day-activities-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 2rem;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
}

.day-activities-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.day-activities-header h3 {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.close-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 1);
}

.day-activities-list {
    margin-bottom: 1.5rem;
}

.day-activity-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.day-activity-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.day-activity-header h4 {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 1.1rem;
}

.day-activity-actions {
    display: flex;
    gap: 0.5rem;
}

.day-activity-item p {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.5rem;
}

.day-activity-item small {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9rem;
}

.day-activities-footer {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
</style>
@endsection

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Calendário -->
            <div class="calendar-container">
                <div class="calendar-header">
                    <h2 class="calendar-title">
                        <i class="fas fa-calendar-alt me-2"></i>Agenda
                    </h2>
                    <div class="calendar-nav">
                        <a href="{{ route('agenda.index', ['month' => date('Y-m', strtotime($currentMonth . '-01 -1 month'))]) }}" 
                           class="calendar-nav-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <span style="color: rgba(255, 255, 255, 0.8); min-width: 120px; text-align: center;">
                            {{ date('F Y', strtotime($currentMonth . '-01')) }}
                        </span>
                        <a href="{{ route('agenda.index', ['month' => date('Y-m', strtotime($currentMonth . '-01 +1 month'))]) }}" 
                           class="calendar-nav-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <button class="calendar-nav-btn" onclick="openEventModal()">
                            <i class="fas fa-plus me-1"></i>Nova Atividade
                        </button>
                    </div>
                </div>

                <div class="calendar-weekdays">
                    <div class="weekday">Dom</div>
                    <div class="weekday">Seg</div>
                    <div class="weekday">Ter</div>
                    <div class="weekday">Qua</div>
                    <div class="weekday">Qui</div>
                    <div class="weekday">Sex</div>
                    <div class="weekday">Sáb</div>
                </div>

                                <div class="calendar-grid">
                    @foreach($calendar as $day)
                        <div class="calendar-day {{ !$day['isCurrentMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}" 
                             onclick="selectDate('{{ $day['date'] }}')">
                            <div class="day-number">{{ $day['day'] }}</div>
                            @if($day['activityCount'] > 0)
                                <div class="day-activities" onclick="event.stopPropagation(); showDayActivities('{{ $day['date'] }}')">
                                    @foreach($day['activities']->take(3) as $activity)
                                        <div class="activity-dot" style="background-color: {{ $activity->color ?? '#007bff' }};" 
                                             title="{{ $activity->title }}"></div>
                                    @endforeach
                                    @if($day['activityCount'] > 3)
                                        <div class="activity-more">+{{ $day['activityCount'] - 3 }}</div>
                                    @endif
                        </div>
                            @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Lista de Atividades -->
            <div class="calendar-container">
                <div class="calendar-header">
                    <h3 class="calendar-title">
                        <i class="fas fa-list me-2"></i>Minhas Atividades
                    </h3>
                </div>

                <div class="activities-list">
                @forelse($agendaItems as $item)
                        <div class="activity-item" data-event-id="{{ $item->id }}" style="border-left: 4px solid {{ $item->color ?? '#007bff' }};">
                            <div class="activity-header">
                                <h5 class="activity-title">{{ $item->title }}</h5>
                                <div class="activity-actions">
                                    <button class="btn btn-sm btn-outline-light" onclick="editEvent({{ $item->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal({{ json_encode($item) }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                    @if(!isset($item->atividade_id) || !$item->atividade_id)
                                        <button class="btn btn-sm btn-outline-success" onclick="createAtividadeFromAgenda({{ $item->id }})" title="Criar Atividade">
                                            <i class="fas fa-tasks"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-info" disabled title="Atividade já criada">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                            </div>
                        </div>
                            <p class="activity-description">{{ $item->description }}</p>
                            <div class="activity-meta">
                                <span class="activity-date">
                                    <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                                </span>
                            @if($item->time)
                                    <span class="activity-time">
                                        <i class="fas fa-clock me-1"></i>{{ $item->time }}
                                    </span>
                            @endif
                                <span class="activity-status status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: rgba(255, 255, 255, 0.6); padding: 2rem;">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>Nenhuma atividade agendada</p>
                        <button class="btn btn-primary" onclick="openEventModal()">
                            <i class="fas fa-plus me-1"></i>Adicionar Primeira Atividade
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
        </div>


    </div>
</div>

<!-- Modal de Evento -->
<div id="eventModal" class="event-modal">
    <div class="event-modal-content">
        <div class="event-modal-header">
            <h3 class="event-modal-title" id="modalTitle">
                <i class="fas fa-plus me-2"></i>Nova Atividade
            </h3>
            <button class="event-modal-close" onclick="closeEventModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="eventForm" onsubmit="saveEvent(event)">
            <input type="hidden" id="eventId" name="id">
            <input type="hidden" id="eventDate" name="date">

            <div class="form-group">
                <label class="form-label" for="eventTitle">Título *</label>
                <input type="text" id="eventTitle" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="eventDescription">Descrição</label>
                <textarea id="eventDescription" name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="eventDateInput">Data *</label>
                    <input type="date" id="eventDateInput" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="eventTime">Horário</label>
                    <input type="time" id="eventTime" name="time" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="eventColor">Cor</label>
                <input type="color" id="eventColor" name="color" class="form-control" value="#007bff">
            </div>

            <div class="form-check">
                <input type="checkbox" id="eventPublic" name="is_public" class="form-check-input">
                <label class="form-check-label" for="eventPublic">
                    Atividade pública (visível no meu perfil)
                </label>
            </div>

            <div class="form-check">
                <input type="checkbox" id="createAtividade" name="create_atividade" class="form-check-input">
                <label class="form-check-label" for="createAtividade">
                    <i class="fas fa-tasks me-1"></i>Criar também como atividade
                </label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeEventModal()">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" id="deleteBtn" class="btn btn-danger" onclick="openDeleteModalFromModal()" style="display: none;">
                    <i class="fas fa-trash me-1"></i>Excluir
                </button>

            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
console.log('Script carregado');
let selectedEventId = null;

function openEventModal(date = null) {
    console.log('openEventModal chamada');
    
    try {
        // Limpar formulário e configurar para nova atividade
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Nova Atividade';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('deleteBtn').style.display = 'none';
    selectedEventId = null;
    
        // Definir data padrão se fornecida
    if (date) {
        document.getElementById('eventDateInput').value = date;
        } else {
            // Definir data atual como padrão
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('eventDateInput').value = today;
        }
        
        // Definir cor padrão
        document.getElementById('eventColor').value = '#007bff';
        
        // Abrir modal
        const modal = document.getElementById('eventModal');
        console.log('Modal encontrado:', modal);
        modal.classList.add('show');
        console.log('Classe show adicionada');
        
        // Focar no primeiro campo
        setTimeout(() => {
            document.getElementById('eventTitle').focus();
        }, 100);
    } catch (error) {
        console.error('Erro ao abrir modal:', error);
        alert('Erro ao abrir modal: ' + error.message);
    }
}

function closeEventModal() {
    document.getElementById('eventModal').classList.remove('show');
}

function editEvent(eventId) {
    // Mostrar loading
    showNotification('Carregando atividade...', 'info');
    
    // Buscar dados da atividade
    fetch(`/agenda/${eventId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(agendaItem => {
            // Preencher formulário
            document.getElementById('eventId').value = agendaItem.id;
            document.getElementById('eventTitle').value = agendaItem.title;
            document.getElementById('eventDescription').value = agendaItem.description || '';
            document.getElementById('eventDateInput').value = agendaItem.date;
            document.getElementById('eventTime').value = agendaItem.time || '';
            document.getElementById('eventColor').value = agendaItem.color || '#007bff';
            document.getElementById('eventPublic').checked = agendaItem.is_public == 1;
            
            // Atualizar título do modal
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Editar Atividade';
            
            // Mostrar botão de excluir
            document.getElementById('deleteBtn').style.display = 'inline-block';
            
            // Definir ID selecionado
            selectedEventId = eventId;
            
            // Abrir modal
            document.getElementById('eventModal').classList.add('show');
            
            // Focar no primeiro campo
            setTimeout(() => {
                document.getElementById('eventTitle').focus();
            }, 100);
        })
        .catch(error => {
            console.error('Erro ao carregar atividade:', error);
            showNotification('Erro ao carregar atividade: ' + error.message, 'error');
        });
}

function selectDate(date) {
    openEventModal(date);
}

function showDayActivities(date) {
    // Buscar atividades do dia
    fetch(`/agenda/day/${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showDayActivitiesModal(date, data.activities);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar atividades do dia:', error);
            showNotification('Erro ao carregar atividades do dia', 'error');
        });
}

function showDayActivitiesModal(date, activities) {
    const modal = document.createElement('div');
    modal.className = 'day-activities-modal';
    modal.innerHTML = `
        <div class="day-activities-content">
            <div class="day-activities-header">
                <h3>Atividades de ${new Date(date).toLocaleDateString('pt-BR')}</h3>
                <button onclick="this.closest('.day-activities-modal').remove()" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="day-activities-list">
                ${activities.length > 0 ? activities.map(activity => `
                    <div class="day-activity-item" style="border-left: 4px solid ${activity.color || '#007bff'}">
                        <div class="day-activity-header">
                            <h4>${activity.title}</h4>
                            <div class="day-activity-actions">
                                <button class="btn btn-sm btn-outline-light" onclick="editEvent(${activity.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${JSON.stringify(activity)})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        ${activity.description ? `<p>${activity.description}</p>` : ''}
                        ${activity.time ? `<small><i class="fas fa-clock"></i> ${activity.time}</small>` : ''}
                    </div>
                `).join('') : '<p class="text-center">Nenhuma atividade neste dia</p>'}
            </div>
            <div class="day-activities-footer">
                <button class="btn btn-primary" onclick="openEventModal('${date}')">
                    <i class="fas fa-plus"></i> Nova Atividade
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}



function saveEvent(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    // Tratar o campo is_public corretamente
    data.is_public = data.is_public === 'on' || data.is_public === true || data.is_public === 'true' || data.is_public === 1 || data.is_public === '1' ? true : false;
    
    // Tratar o campo create_atividade corretamente
    data.create_atividade = data.create_atividade === 'on' || data.create_atividade === true || data.create_atividade === 'true' || data.create_atividade === 1 || data.create_atividade === '1' ? true : false;
    
    // Tratar o campo time - se estiver vazio, definir como null
    if (!data.time || data.time === '') {
        data.time = null;
    }
    
    // Tratar o campo description - se estiver vazio, definir como null
    if (!data.description || data.description === '') {
        data.description = null;
    }
    
    // Tratar o campo color - se estiver vazio, usar cor padrão
    if (!data.color || data.color === '') {
        data.color = '#007bff';
    }
    
    const url = selectedEventId ? `/agenda/${selectedEventId}` : '/agenda';
    const method = selectedEventId ? 'PUT' : 'POST';
    
    // Mostrar loading no botão
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';
    submitBtn.disabled = true;
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(responseData => {
        if (responseData.success) {
            closeEventModal();
            
            if (selectedEventId) {
                // Atualizar atividade existente
                updateEventInList(responseData.agendaItem);
        } else {
                // Adicionar nova atividade
                addEventToList(responseData.agendaItem);
            }
            
            // Mostrar notificação de sucesso
            showNotification('Atividade salva com sucesso!', 'success');
        } else {
            throw new Error(responseData.error || 'Erro desconhecido');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        showNotification('Erro ao salvar atividade: ' + error.message, 'error');
    })
    .finally(() => {
        // Restaurar botão
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function createAtividadeFromAgenda(agendaItemId) {
    if (confirm('Deseja criar uma atividade a partir deste item da agenda?')) {
        // Feedback visual imediato
        const button = document.querySelector(`button[onclick="createAtividadeFromAgenda(${agendaItemId})"]`);
        if (button) {
            const originalContent = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        fetch(`/agenda/${agendaItemId}/create-atividade`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar o botão na interface
                if (button) {
                    button.className = 'btn btn-sm btn-outline-info';
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.title = 'Atividade já criada';
                    button.onclick = null;
                }
                
                showNotification('Atividade criada com sucesso!', 'success');
            } else {
                // Restaurar botão em caso de erro
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
                showNotification(data.message || 'Erro ao criar atividade', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao criar atividade:', error);
            // Restaurar botão em caso de erro
            if (button) {
                button.disabled = false;
                button.innerHTML = originalContent;
            }
            showNotification('Erro ao criar atividade', 'error');
        });
    }
}

function addEventToList(agendaItem) {
    // Remover mensagem "Nenhuma atividade agendada" se existir
    const emptyMessage = document.querySelector('.activities-list .text-center');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    // Criar elemento da atividade
    const eventElement = createEventElement(agendaItem);
    
    // Adicionar com animação
    eventElement.style.opacity = '0';
    eventElement.style.transform = 'translateY(-20px)';
    
    // Encontrar o container de atividades
    const activitiesContainer = document.querySelector('.activities-list');
    if (activitiesContainer) {
        activitiesContainer.appendChild(eventElement);
        
        // Animar entrada
        setTimeout(() => {
            eventElement.style.transition = 'all 0.5s ease';
            eventElement.style.opacity = '1';
            eventElement.style.transform = 'translateY(0)';
        }, 10);
    }
    
    // Atualizar calendário
    updateCalendarWithNewEvent(agendaItem);
}

function updateEventInList(agendaItem) {
    // Encontrar elemento existente
    const existingElement = document.querySelector(`[data-event-id="${agendaItem.id}"]`);
    if (existingElement) {
        // Atualizar conteúdo
        const newElement = createEventElement(agendaItem);
        existingElement.innerHTML = newElement.innerHTML;
        existingElement.className = newElement.className;
        existingElement.setAttribute('data-event-id', agendaItem.id);
        
        // Adicionar animação de atualização
        existingElement.style.animation = 'pulse 0.5s ease';
        setTimeout(() => {
            existingElement.style.animation = '';
        }, 500);
    } else {
        // Se não encontrou o elemento, pode ser que seja uma nova atividade
        // Remover mensagem "Nenhuma atividade agendada" se existir
        const emptyMessage = document.querySelector('.activities-list .text-center');
        if (emptyMessage) {
            emptyMessage.remove();
        }
        
        // Adicionar como nova atividade
        addEventToList(agendaItem);
    }
}

function createEventElement(agendaItem) {
    const eventDiv = document.createElement('div');
    eventDiv.className = 'activity-item';
    eventDiv.setAttribute('data-event-id', agendaItem.id);
    eventDiv.style.borderLeft = `4px solid ${agendaItem.color || '#007bff'}`;
    
    const date = new Date(agendaItem.date);
    const formattedDate = date.toLocaleDateString('pt-BR');
    const time = agendaItem.time ? agendaItem.time : '';
    
    eventDiv.innerHTML = `
        <div class="activity-header">
            <h5 class="activity-title">${agendaItem.title}</h5>
            <div class="activity-actions">
                <button class="btn btn-sm btn-outline-light" onclick="editEvent(${agendaItem.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${JSON.stringify(agendaItem)})">
                    <i class="fas fa-trash"></i>
                </button>
                ${!agendaItem.atividade_id ? `
                    <button class="btn btn-sm btn-outline-success" onclick="createAtividadeFromAgenda(${agendaItem.id})" title="Criar Atividade">
                        <i class="fas fa-tasks"></i>
                    </button>
                ` : `
                    <button class="btn btn-sm btn-outline-info" disabled title="Atividade já criada">
                        <i class="fas fa-check"></i>
                    </button>
                `}
            </div>
        </div>
        <p class="activity-description">${agendaItem.description || ''}</p>
        <div class="activity-meta">
            <span class="activity-date">
                <i class="fas fa-calendar me-1"></i>${formattedDate}
            </span>
            ${time ? `<span class="activity-time"><i class="fas fa-clock me-1"></i>${time}</span>` : ''}
            <span class="activity-status status-${agendaItem.status}">${getStatusText(agendaItem.status)}</span>
        </div>
    `;
    
    return eventDiv;
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Pendente',
        'completed': 'Concluída',
        'cancelled': 'Cancelada'
    };
    return statusMap[status] || status;
}

function updateCalendarWithNewEvent(agendaItem) {
    // Encontrar o dia no calendário
    const eventDate = new Date(agendaItem.date);
    const dayElements = document.querySelectorAll('.calendar-day');
    
    dayElements.forEach(dayElement => {
        const dayNumber = dayElement.querySelector('.day-number');
        if (dayNumber) {
            // Verificar se o dia é do mês atual
            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();
            
            // Tentar encontrar o dia correto no calendário
            const dayText = dayNumber.textContent.trim();
            if (dayText && !isNaN(parseInt(dayText))) {
                const dayDate = new Date(currentYear, currentMonth, parseInt(dayText));
                
                // Comparar apenas o dia e mês (ignorar ano para simplificar)
                if (dayDate.getDate() === eventDate.getDate() && dayDate.getMonth() === eventDate.getMonth()) {
                    // Adicionar indicador de atividade no dia
                    let dayActivities = dayElement.querySelector('.day-activities');
                    if (!dayActivities) {
                        dayActivities = document.createElement('div');
                        dayActivities.className = 'day-activities';
                        dayElement.appendChild(dayActivities);
                    }
                    
                    // Verificar se já existe um indicador para esta atividade
                    const existingDot = dayActivities.querySelector(`[data-event-id="${agendaItem.id}"]`);
                    if (!existingDot) {
                        // Criar indicador de atividade
                        const activityDot = document.createElement('div');
                        activityDot.className = 'activity-dot';
                        activityDot.setAttribute('data-event-id', agendaItem.id);
                        activityDot.style.backgroundColor = agendaItem.color || '#007bff';
                        activityDot.title = agendaItem.title;
                        activityDot.onclick = (e) => {
                            e.stopPropagation();
                            showDayActivities(agendaItem.date);
                        };
                        
                        dayActivities.appendChild(activityDot);
                        
                        // Adicionar classe para indicar que o dia tem eventos
                        dayElement.classList.add('has-events');
                    }
                }
            }
        }
    });
}

function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Adicionar ao body
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function deleteEvent(eventId = null) {
    const id = eventId || selectedEventId;
    
    if (!id) {
        showNotification('Nenhuma atividade selecionada para excluir', 'error');
        return;
    }
    
    // Confirmação mais elegante
    if (confirm('Tem certeza que deseja excluir esta atividade?\n\nEsta ação não pode ser desfeita.')) {
        // Mostrar loading
        showNotification('Excluindo atividade...', 'info');
        
        fetch(`/agenda/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
    })
    .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        return response.json();
    })
        .then(data => {
            if (data.success) {
                // Remover elemento da lista com animação
                const eventElement = document.querySelector(`[data-event-id="${id}"]`);
                if (eventElement) {
                    eventElement.style.transition = 'all 0.3s ease';
                    eventElement.style.opacity = '0';
                    eventElement.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        eventElement.remove();
                    }, 300);
                }
                
                closeEventModal();
                showNotification('Atividade excluída com sucesso!', 'success');
        } else {
                throw new Error(data.error || 'Erro ao excluir atividade');
            }
    })
    .catch(error => {
            console.error('Erro ao excluir atividade:', error);
            showNotification('Erro ao excluir atividade: ' + error.message, 'error');
        });
    }
}

// Variável global para atividade sendo deletada
let deletingAgendaItem = null;

// Função para abrir modal de drag and drop a partir do modal de edição
function openDeleteModalFromModal() {
    if (selectedEventId) {
        // Buscar os dados da atividade atual
        const eventElement = document.querySelector(`[data-event-id="${selectedEventId}"]`);
        if (eventElement) {
            // Extrair dados do elemento
            const title = eventElement.querySelector('.activity-title').textContent;
            const description = eventElement.querySelector('.activity-description').textContent;
            const date = eventElement.querySelector('.activity-date').textContent.replace('📅 ', '');
            const time = eventElement.querySelector('.activity-time')?.textContent.replace('🕐 ', '') || null;
            const status = eventElement.querySelector('.activity-status').textContent;
            const color = eventElement.style.borderLeftColor || '#007bff';
            
            const agendaItem = {
                id: selectedEventId,
                title: title,
                description: description,
                date: date,
                time: time,
                status: status,
                color: color
            };
            
            openDeleteModal(agendaItem);
        }
    }
}

// Função para abrir modal de drag and drop
function openDeleteModal(agendaItem) {
    deletingAgendaItem = agendaItem;
    
    // Clonar o card da atividade
    const originalCard = document.querySelector(`[data-event-id="${agendaItem.id}"]`);
    if (originalCard) {
        const clonedCard = originalCard.cloneNode(true);
        
        // Limpar o container e adicionar o card clonado
        document.getElementById('deleteActivityCard').innerHTML = '';
        document.getElementById('deleteActivityCard').appendChild(clonedCard);
        
        // Configurar drag and drop
        setupDragAndDrop();
        
        // Mostrar overlay
        document.getElementById('deleteOverlay').classList.add('show');
        
        // Adicionar classe pulse na seta
        const arrow = document.querySelector('.delete-arrow');
        if (arrow) {
            arrow.classList.add('pulse');
        }
        
        console.log('Modal de remoção aberto para:', agendaItem);
    }
}

// Função para fechar modal de drag and drop
function closeDeleteModal() {
    document.getElementById('deleteOverlay').classList.remove('show');
    deletingAgendaItem = null;
    
    // Limpar classes da lixeira
    const deleteTrash = document.getElementById('deleteTrash');
    if (deleteTrash) {
        deleteTrash.classList.remove('active', 'highlight');
    }
    
    // Remover classe pulse da seta
    const arrow = document.querySelector('.delete-arrow');
    if (arrow) {
        arrow.classList.remove('pulse');
    }
    
    console.log('Modal de remoção fechado');
}

// Configurar drag and drop
function setupDragAndDrop() {
    const deleteCard = document.getElementById('deleteActivityCard');
    const deleteTrash = document.getElementById('deleteTrash');
    
    if (deleteCard && deleteTrash) {
        // Tornar o card arrastável
        deleteCard.draggable = true;
        
        deleteCard.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', '');
            this.classList.add('dragging');
            console.log('Iniciando arrasto do card da agenda');
        });
        
        deleteCard.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            console.log('Finalizando arrasto do card da agenda');
        });
        
        deleteTrash.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('active');
            console.log('Arrasto sobre a lixeira da agenda');
        });
        
        deleteTrash.addEventListener('dragleave', function(e) {
            this.classList.remove('active');
            console.log('Saindo da lixeira da agenda');
        });
        
        deleteTrash.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('active');
            this.classList.add('highlight');
            
            console.log('Soltando na lixeira da agenda - confirmando remoção');
            
            // Confirmar remoção
            if (confirm('Tem certeza que deseja remover esta atividade da agenda?')) {
                removeAgendaItem(deletingAgendaItem.id);
            } else {
                this.classList.remove('highlight');
            }
        });
        
        // Adicionar evento de clique na lixeira como alternativa
        deleteTrash.addEventListener('click', function() {
            if (deletingAgendaItem) {
                this.classList.add('highlight');
                if (confirm('Tem certeza que deseja remover esta atividade da agenda?')) {
                    removeAgendaItem(deletingAgendaItem.id);
                } else {
                    this.classList.remove('highlight');
                }
            }
        });
    }
}

// Função para remover item da agenda
function removeAgendaItem(id) {
    console.log('Iniciando remoção da atividade da agenda:', id);
    
    // Adicionar efeito visual de remoção
    const deleteCard = document.getElementById('deleteActivityCard');
    if (deleteCard) {
        deleteCard.style.transform = 'scale(0.8) rotate(5deg)';
        deleteCard.style.opacity = '0.5';
        deleteCard.style.transition = 'all 0.3s ease';
    }
    
    fetch(`/agenda/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover da lista
            const eventElement = document.querySelector(`[data-event-id="${id}"]`);
            if (eventElement) {
                eventElement.style.transition = 'all 0.3s ease';
                eventElement.style.opacity = '0';
                eventElement.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    eventElement.remove();
                    
                    // Verificar se ainda há atividades na lista
                    const remainingActivities = document.querySelectorAll('.activities-list .activity-item');
                    if (remainingActivities.length === 0) {
                        // Mostrar mensagem "Nenhuma atividade agendada"
                        const activitiesContainer = document.querySelector('.activities-list');
                        if (activitiesContainer) {
                            activitiesContainer.innerHTML = `
                                <div style="text-align: center; color: rgba(255, 255, 255, 0.6); padding: 2rem;">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>Nenhuma atividade agendada</p>
                                    <button class="btn btn-primary" onclick="openEventModal()">
                                        <i class="fas fa-plus me-1"></i>Adicionar Primeira Atividade
                                    </button>
                                </div>
                            `;
                        }
                    }
                }, 300);
            }
            
            // Efeito de sucesso na lixeira
            const deleteTrash = document.getElementById('deleteTrash');
            if (deleteTrash) {
                deleteTrash.style.transform = 'scale(1.2)';
                deleteTrash.style.background = 'rgba(76, 175, 80, 0.3)';
                deleteTrash.style.borderColor = '#4caf50';
                setTimeout(() => {
                    deleteTrash.style.transform = 'scale(1)';
                    deleteTrash.style.borderColor = '';
                }, 500);
            }
            
            setTimeout(() => {
                closeDeleteModal();
                showNotification('Atividade da agenda removida com sucesso!', 'success');
            }, 300);
        } else {
            console.error('Erro na resposta:', data.error);
            showNotification('Erro ao remover atividade da agenda: ' + data.error, 'error');
            
            // Restaurar card se houver erro
            if (deleteCard) {
                deleteCard.style.transform = '';
                deleteCard.style.opacity = '';
            }
        }
    })
    .catch(error => {
        console.error('Erro ao remover atividade da agenda:', error);
        showNotification('Erro ao remover atividade da agenda', 'error');
        
        // Restaurar card se houver erro
        if (deleteCard) {
            deleteCard.style.transform = '';
            deleteCard.style.opacity = '';
        }
    });
}

// Fechar modal ao clicar fora
document.getElementById('eventModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEventModal();
    }
});
</script>

<!-- Overlay de Remoção -->
<div id="deleteOverlay" class="delete-overlay">
    <div class="delete-overlay-background"></div>
    <div class="delete-container">
        <div class="delete-header">
            <h4><i class="fas fa-trash me-2"></i>Remover Atividade da Agenda</h4>
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

@endsection 
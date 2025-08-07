```blade
@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.events.create'))

@push('styles')
<style>
.form-card {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 25px;
    margin-bottom: 25px;
}

.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.section-title {
    color: #495057;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-row.two-columns {
    grid-template-columns: 1fr 1fr;
}

.form-row.three-columns {
    grid-template-columns: 1fr 1fr 1fr;
}

.event-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.event-type-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.event-type-card:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.event-type-card.selected {
    border-color: #007bff;
    background: #e3f2fd;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.type-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
    display: block;
}

.type-name {
    font-weight: bold;
    margin-bottom: 5px;
    color: #495057;
}

.type-description {
    font-size: 0.85rem;
    color: #6c757d;
}

.datetime-inputs {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 10px;
}

.spice-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon .form-control {
    padding-left: 35px;
}

.input-with-icon .input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.form-help {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 5px;
}

.form-check-custom {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.preview-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.preview-event {
    background: white;
    border-radius: 6px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

@media (max-width: 768px) {
    .form-row,
    .form-row.two-columns,
    .form-row.three-columns {
        grid-template-columns: 1fr;
    }
    
    .datetime-inputs,
    .spice-inputs {
        grid-template-columns: 1fr;
    }
    
    .event-type-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    {{ trans('dune-rp::admin.events.create') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dune-rp.admin.events.index') }}">{{ trans('dune-rp::admin.events.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.events.create') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="form-card">
            @if($errors->any())
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> {{ trans('dune-rp::admin.errors.validation') }}</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dune-rp.admin.events.store') }}" method="POST" id="createEventForm">
                @csrf

                {{-- Basic Information --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        {{ trans('dune-rp::admin.events.basic_info') }}
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="title" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.title') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" id="title" name="title" class="form-control" 
                                       value="{{ old('title') }}" required maxlength="255"
                                       placeholder="{{ trans('dune-rp::admin.events.placeholders.title') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.title') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="organizer_id" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.organizer') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <select id="organizer_id" name="organizer_id" class="form-control" required>
                                    <option value="">{{ trans('dune-rp::admin.events.select_organizer') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('organizer_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.organizer') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="organizer_house_id" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.organizer_house') }}
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-shield-alt input-icon"></i>
                                <select id="organizer_house_id" name="organizer_house_id" class="form-control">
                                    <option value="">{{ trans('dune-rp::admin.events.no_house') }}</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ old('organizer_house_id') == $house->id ? 'selected' : '' }}>
                                            {{ $house->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.house') }}</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            {{ trans('dune-rp::admin.events.fields.description') }}
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="5" maxlength="5000"
                                  placeholder="{{ trans('dune-rp::admin.events.placeholders.description') }}">{{ old('description') }}</textarea>
                        <small class="form-help">{{ trans('dune-rp::admin.events.help.description') }}</small>
                        <div id="description-counter" class="form-help text-right">0/5000</div>
                    </div>
                </div>

                {{-- Event Type --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-tags"></i>
                        {{ trans('dune-rp::admin.events.event_type') }}
                    </h4>

                    <div class="event-type-grid">
                        <div class="event-type-card" data-type="meeting">
                            <i class="fas fa-users type-icon" style="color: #28a745;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.meeting') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.meeting_desc') }}</div>
                        </div>

                        <div class="event-type-card" data-type="battle">
                            <i class="fas fa-sword type-icon" style="color: #dc3545;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.battle') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.battle_desc') }}</div>
                        </div>

                        <div class="event-type-card" data-type="ceremony">
                            <i class="fas fa-crown type-icon" style="color: #ffc107;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.ceremony') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.ceremony_desc') }}</div>
                        </div>

                        <div class="event-type-card" data-type="trade">
                            <i class="fas fa-coins type-icon" style="color: #fd7e14;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.trade') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.trade_desc') }}</div>
                        </div>

                        <div class="event-type-card" data-type="exploration">
                            <i class="fas fa-compass type-icon" style="color: #17a2b8;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.exploration') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.exploration_desc') }}</div>
                        </div>

                        <div class="event-type-card" data-type="other">
                            <i class="fas fa-question-circle type-icon" style="color: #6c757d;"></i>
                            <div class="type-name">{{ trans('dune-rp::admin.events.types.other') }}</div>
                            <div class="type-description">{{ trans('dune-rp::admin.events.types.other_desc') }}</div>
                        </div>
                    </div>

                    <input type="hidden" id="event_type" name="event_type" value="{{ old('event_type') }}" required>
                </div>

                {{-- Date and Location --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        {{ trans('dune-rp::admin.events.scheduling') }}
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_date" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.event_date') }} <span class="text-danger">*</span>
                            </label>
                            <div class="datetime-inputs">
                                <input type="date" id="event_date" name="event_date" class="form-control" 
                                       value="{{ old('event_date') }}" required>
                                <input type="time" id="event_time" name="event_time" class="form-control" 
                                       value="{{ old('event_time', '20:00') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.date') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.location') }}
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" id="location" name="location" class="form-control" 
                                       value="{{ old('location') }}" maxlength="255"
                                       placeholder="{{ trans('dune-rp::admin.events.placeholders.location') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.location') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.status') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-flag input-icon"></i>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="planned" {{ old('status', 'planned') === 'planned' ? 'selected' : '' }}>
                                        {{ trans('dune-rp::admin.events.status.planned') }}
                                    </option>
                                    <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>
                                        {{ trans('dune-rp::admin.events.status.ongoing') }}
                                    </option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>
                                        {{ trans('dune-rp::admin.events.status.completed') }}
                                    </option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                        {{ trans('dune-rp::admin.events.status.cancelled') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Participants and Settings --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-users"></i>
                        {{ trans('dune-rp::admin.events.participants_settings') }}
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_participants" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.max_participants') }}
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-users input-icon"></i>
                                <input type="number" id="max_participants" name="max_participants" class="form-control" 
                                       value="{{ old('max_participants') }}" min="1" max="1000"
                                       placeholder="{{ trans('dune-rp::admin.events.unlimited') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.max_participants') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ trans('dune-rp::admin.events.visibility') }}</label>
                            <div class="form-check-custom">
                                <input type="checkbox" id="is_public" name="is_public" value="1" 
                                       {{ old('is_public', true) ? 'checked' : '' }} class="form-check-input">
                                <label for="is_public" class="form-check-label">
                                    <i class="fas fa-globe"></i>
                                    {{ trans('dune-rp::admin.events.fields.is_public') }}
                                </label>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.is_public') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Spice Economics --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-coins"></i>
                        {{ trans('dune-rp::admin.events.spice_economics') }}
                    </h4>

                    <div class="spice-inputs">
                        <div class="form-group">
                            <label for="spice_cost" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.spice_cost') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-minus-circle text-danger"></i>
                                    </span>
                                </div>
                                <input type="number" id="spice_cost" name="spice_cost" class="form-control" 
                                       value="{{ old('spice_cost', 0) }}" min="0" max="10000" step="0.01">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ trans('dune-rp::admin.spice.unit') }}</span>
                                </div>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.spice_cost') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="reward_spice" class="form-label">
                                {{ trans('dune-rp::admin.events.fields.reward_spice') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-plus-circle text-success"></i>
                                    </span>
                                </div>
                                <input type="number" id="reward_spice" name="reward_spice" class="form-control" 
                                       value="{{ old('reward_spice', 0) }}" min="0" max="100000" step="0.01">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ trans('dune-rp::admin.spice.unit') }}</span>
                                </div>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.events.help.reward_spice') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Preview Section --}}
                <div class="preview-section" id="eventPreview" style="display: none;">
                    <h4 style="margin-bottom: 15px;">
                        <i class="fas fa-eye"></i>
                        {{ trans('dune-rp::admin.events.preview') }}
                    </h4>
                    <div id="previewContent" class="preview-event"></div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions" style="text-align: center; padding-top: 30px; border-top: 1px solid #dee2e6;">
                    <button type="button" id="previewBtn" class="btn btn-info">
                        <i class="fas fa-eye"></i>
                        {{ trans('dune-rp::admin.events.preview') }}
                    </button>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        {{ trans('dune-rp::admin.events.create') }}
                    </button>
                    
                    <a href="{{ route('dune-rp.admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        {{ trans('admin.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event type selection
    const typeCards = document.querySelectorAll('.event-type-card');
    const eventTypeInput = document.getElementById('event_type');
    
    typeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            typeCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Set the hidden input value
            eventTypeInput.value = this.dataset.type;
        });
    });
    
    // Pre-select if old value exists
    if (eventTypeInput.value) {
        const selectedCard = document.querySelector(`[data-type="${eventTypeInput.value}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
    }

    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const counter = document.getElementById('description-counter');
    
    descriptionTextarea.addEventListener('input', function() {
        const current = this.value.length;
        const max = 5000;
        counter.textContent = `${current}/${max}`;
        
        if (current > max * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    });

    // Combine date and time for datetime field
    const form = document.getElementById('createEventForm');
    form.addEventListener('submit', function(e) {
        const dateInput = document.getElementById('event_date');
        const timeInput = document.getElementById('event_time');
        
        if (dateInput.value && timeInput.value) {
            // Create a hidden input with the combined datetime
            const datetimeInput = document.createElement('input');
            datetimeInput.type = 'hidden';
            datetimeInput.name = 'event_date';
            datetimeInput.value = dateInput.value + ' ' + timeInput.value;
            
            // Remove the name attribute from original inputs to avoid conflicts
            dateInput.removeAttribute('name');
            timeInput.removeAttribute('name');
            
            // Append the datetime input
            form.appendChild(datetimeInput);
        }
    });

    // Preview functionality
    const previewBtn = document.getElementById('previewBtn');
    const previewPanel = document.getElementById('eventPreview');
    const previewContent = document.getElementById('previewContent');
    
    previewBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        
        // Build preview HTML
        let preview = '';
        
        // Title
        const title = formData.get('title') || 'Titre de l\'événement';
        preview += `<h5 style="color: #007bff; margin-bottom: 15px;">${title}</h5>`;
        
        // Badges
        preview += '<div style="margin-bottom: 15px;">';
        
        // Status badge
        const status = formData.get('status') || 'planned';
        const statusColors = {
            planned: '#007bff',
            ongoing: '#28a745',
            completed: '#6c757d',
            cancelled: '#dc3545'
        };
        preview += `<span class="badge" style="background: ${statusColors[status]}; color: white; margin-right: 8px;">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        
        // Type badge
        if (eventTypeInput.value) {
            const selectedCard = document.querySelector(`[data-type="${eventTypeInput.value}"]`);
            if (selectedCard) {
                const typeName = selectedCard.querySelector('.type-name').textContent;
                preview += `<span class="badge badge-secondary">${typeName}</span>`;
            }
        }
        
        preview += '</div>';
        
        // Meta info
        const eventDate = document.getElementById('event_date').value;
        const eventTime = document.getElementById('event_time').value;
        const location = formData.get('location');
        const organizerName = document.querySelector('#organizer_id option:checked')?.textContent || '';
        const houseName = document.querySelector('#organizer_house_id option:checked')?.textContent || '';
        
        if (eventDate || location || organizerName) {
            preview += '<div style="margin: 15px 0; font-size: 0.9rem; color: #6c757d;">';
            
            if (eventDate && eventTime) {
                const dateObj = new Date(eventDate + 'T' + eventTime);
                preview += `<div><i class="fas fa-calendar"></i> ${dateObj.toLocaleDateString('fr-FR')} à ${dateObj.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>`;
            }
            
            if (location) {
                preview += `<div><i class="fas fa-map-marker-alt"></i> ${location}</div>`;
            }
            
            if (organizerName) {
                preview += `<div><i class="fas fa-user"></i> ${organizerName}${houseName && houseName !== 'Aucune maison' ? ` (${houseName})` : ''}</div>`;
            }
            
            preview += '</div>';
        }
        
        // Description
        const description = formData.get('description');
        if (description) {
            preview += `<div style="margin: 15px 0; line-height: 1.6;">${description.replace(/\n/g, '<br>')}</div>`;
        }
        
        // Economics
        const spiceCost = formData.get('spice_cost');
        const rewardSpice = formData.get('reward_spice');
        if (spiceCost > 0 || rewardSpice > 0) {
            preview += '<div style="margin: 15px 0; font-size: 0.9rem;">';
            if (spiceCost > 0) {
                preview += `<span style="color: #dc3545;"><i class="fas fa-minus-circle"></i> Coût: ${spiceCost} Épice</span> `;
            }
            if (rewardSpice > 0) {
                preview += `<span style="color: #28a745;"><i class="fas fa-plus-circle"></i> Récompense: ${rewardSpice} Épice</span>`;
            }
            preview += '</div>';
        }
        
        previewContent.innerHTML = preview;
        previewPanel.style.display = previewPanel.style.display === 'none' ? 'block' : 'none';
        
        if (previewPanel.style.display === 'block') {
            previewPanel.scrollIntoView({ behavior: 'smooth' });
        }
    });

    // Auto-fill current date if empty
    const dateInput = document.getElementById('event_date');
    if (!dateInput.value) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Check required fields
        if (!document.getElementById('title').value.trim()) {
            errors.push('Le titre est requis');
            isValid = false;
        }

        if (!document.getElementById('organizer_id').value) {
            errors.push('L\'organisateur est requis');
            isValid = false;
        }

        if (!eventTypeInput.value) {
            errors.push('Le type d\'événement est requis');
            isValid = false;
        }

        if (!document.getElementById('event_date').value) {
            errors.push('La date est requise');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Erreurs de validation:\n' + errors.join('\n'));
        }
    });
});
</script>
@endpush
```

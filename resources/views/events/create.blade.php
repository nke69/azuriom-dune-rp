```blade
@extends('layouts.app')

@section('title', trans('dune-rp::messages.events.create') . ' - ' . trans('dune-rp::messages.events.title'))

@push('styles')
<style>
:root {
    --dune-sand: #D6A65F;
    --dune-sand-dark: #B8935A;
    --dune-spice: #FF8C42;
    --dune-spice-glow: #FFA500;
    --dune-desert: #8B4513;
    --dune-night: #0F0F23;
    --font-dune: 'Cinzel', serif;
}

.create-header {
    background: linear-gradient(135deg, var(--dune-night), var(--dune-desert));
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.create-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 50%, rgba(255,165,0,0.2), transparent 70%);
    pointer-events: none;
}

.dune-form {
    background: linear-gradient(145deg, rgba(15,15,35,0.95), rgba(139,69,19,0.1));
    border: 2px solid var(--dune-sand);
    border-radius: 12px;
    padding: 30px;
    color: var(--dune-sand);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    color: var(--dune-spice-glow);
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
    font-family: var(--font-dune);
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--dune-sand-dark);
    border-radius: 8px;
    background: rgba(15,15,35,0.7);
    color: var(--dune-sand);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--dune-spice);
    box-shadow: 0 0 0 3px rgba(255,140,66,0.2);
    outline: none;
    background: rgba(15,15,35,0.9);
    color: white;
}

.form-control::placeholder {
    color: var(--dune-sand-dark);
}

select.form-control option {
    background: var(--dune-night);
    color: var(--dune-sand);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.form-text {
    color: var(--dune-sand-dark);
    font-size: 0.9rem;
    margin-top: 5px;
}

.input-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.input-group-text {
    background: var(--dune-sand-dark);
    color: white;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    min-width: 60px;
    text-align: center;
}

.dune-button {
    background: linear-gradient(45deg, var(--dune-spice), var(--dune-spice-glow));
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-family: var(--font-dune);
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255,140,66,0.4);
    font-size: 1.1rem;
    cursor: pointer;
}

.dune-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,140,66,0.6);
    color: white;
    text-decoration: none;
}

.dune-button.secondary {
    background: linear-gradient(45deg, var(--dune-sand-dark), var(--dune-sand));
    box-shadow: 0 4px 15px rgba(214,166,95,0.4);
}

.event-type-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.event-type-card {
    border: 2px solid var(--dune-sand-dark);
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(214,166,95,0.1);
    text-align: center;
}

.event-type-card:hover {
    border-color: var(--dune-spice);
    background: rgba(255,140,66,0.1);
    transform: translateY(-2px);
}

.event-type-card.selected {
    border-color: var(--dune-spice);
    background: rgba(255,140,66,0.2);
    box-shadow: 0 0 15px rgba(255,140,66,0.3);
}

.type-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.datetime-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.cost-reward-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

@media (max-width: 768px) {
    .create-header {
        padding: 25px 20px;
    }
    
    .dune-form {
        padding: 20px;
    }
    
    .datetime-inputs,
    .cost-reward-inputs {
        grid-template-columns: 1fr;
    }
    
    .event-type-cards {
        grid-template-columns: 1fr;
    }
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-danger {
    background: rgba(244, 67, 54, 0.1);
    border: 1px solid #f44336;
    color: #ffcdd2;
}

.preview-panel {
    background: rgba(214,166,95,0.1);
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid var(--dune-sand-dark);
}
</style>
@endpush

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="create-header">
        <h1 style="color: var(--dune-spice-glow); font-family: var(--font-dune); font-size: 2.5rem; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
            <i class="bi bi-calendar-plus"></i> {{ trans('dune-rp::messages.events.create') }}
        </h1>
        <p style="color: var(--dune-sand); margin: 15px 0 0 0; font-size: 1.2rem;">
            {{ trans('dune-rp::messages.events.create_subtitle') }}
        </p>
    </div>

    {{-- Create Form --}}
    <div class="dune-form">
        @if($errors->any())
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle"></i> {{ trans('dune-rp::messages.errors.form_errors') }}</h5>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dune-rp.events.store') }}" method="POST" id="createEventForm">
            @csrf

            {{-- Basic Information --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title" class="form-label">
                            <i class="bi bi-card-heading"></i> {{ trans('dune-rp::messages.events.fields.title') }} *
                        </label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="{{ old('title') }}" required maxlength="255"
                               placeholder="{{ trans('dune-rp::messages.events.placeholders.title') }}">
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.title') }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="organizer_house_id" class="form-label">
                            <i class="bi bi-shield"></i> {{ trans('dune-rp::messages.events.fields.organizer_house') }}
                        </label>
                        <select id="organizer_house_id" name="organizer_house_id" class="form-control">
                            <option value="">{{ trans('dune-rp::messages.events.no_house') }}</option>
                            @foreach($houses as $house)
                                <option value="{{ $house->id }}" {{ old('organizer_house_id') == $house->id ? 'selected' : '' }}>
                                    {{ $house->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.house') }}</div>
                    </div>
                </div>
            </div>

            {{-- Event Type Selection --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-tag"></i> {{ trans('dune-rp::messages.events.fields.event_type') }} *
                </label>
                <div class="event-type-cards">
                    <div class="event-type-card" data-type="meeting">
                        <i class="bi bi-people type-icon" style="color: #4caf50;"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.meeting') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.meeting_desc') }}
                        </div>
                    </div>

                    <div class="event-type-card" data-type="battle">
                        <i class="bi bi-sword type-icon" style="color: #f44336;"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.battle') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.battle_desc') }}
                        </div>
                    </div>

                    <div class="event-type-card" data-type="ceremony">
                        <i class="bi bi-award type-icon" style="color: #ff9800;"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.ceremony') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.ceremony_desc') }}
                        </div>
                    </div>

                    <div class="event-type-card" data-type="trade">
                        <i class="bi bi-coin type-icon" style="color: var(--dune-spice-glow);"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.trade') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.trade_desc') }}
                        </div>
                    </div>

                    <div class="event-type-card" data-type="exploration">
                        <i class="bi bi-compass type-icon" style="color: #2196f3;"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.exploration') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.exploration_desc') }}
                        </div>
                    </div>

                    <div class="event-type-card" data-type="other">
                        <i class="bi bi-question-circle type-icon" style="color: #9e9e9e;"></i>
                        <div style="font-weight: bold; color: white;">{{ trans('dune-rp::messages.events.types.other') }}</div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand-dark); margin-top: 5px;">
                            {{ trans('dune-rp::messages.events.types.other_desc') }}
                        </div>
                    </div>
                </div>
                <input type="hidden" id="event_type" name="event_type" value="{{ old('event_type') }}" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label for="description" class="form-label">
                    <i class="bi bi-textarea-resize"></i> {{ trans('dune-rp::messages.events.fields.description') }} *
                </label>
                <textarea id="description" name="description" class="form-control" required maxlength="5000"
                          placeholder="{{ trans('dune-rp::messages.events.placeholders.description') }}">{{ old('description') }}</textarea>
                <div class="form-text">{{ trans('dune-rp::messages.events.help.description') }}</div>
            </div>

            {{-- Date and Location --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="event_date" class="form-label">
                            <i class="bi bi-calendar-date"></i> {{ trans('dune-rp::messages.events.fields.date') }} *
                        </label>
                        <div class="datetime-inputs">
                            <input type="date" id="event_date" name="event_date" class="form-control" 
                                   value="{{ old('event_date') }}" required min="{{ now()->format('Y-m-d') }}">
                            <input type="time" id="event_time" name="event_time" class="form-control" 
                                   value="{{ old('event_time', '20:00') }}">
                        </div>
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.date') }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location" class="form-label">
                            <i class="bi bi-geo-alt"></i> {{ trans('dune-rp::messages.events.fields.location') }}
                        </label>
                        <input type="text" id="location" name="location" class="form-control" 
                               value="{{ old('location') }}" maxlength="255"
                               placeholder="{{ trans('dune-rp::messages.events.placeholders.location') }}">
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.location') }}</div>
                    </div>
                </div>
            </div>

            {{-- Participants and Economics --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="max_participants" class="form-label">
                            <i class="bi bi-people"></i> {{ trans('dune-rp::messages.events.fields.max_participants') }}
                        </label>
                        <input type="number" id="max_participants" name="max_participants" class="form-control" 
                               value="{{ old('max_participants') }}" min="1" max="100"
                               placeholder="{{ trans('dune-rp::messages.events.placeholders.max_participants') }}">
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.max_participants') }}</div>
                    </div>

                    <div class="form-group">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <input type="checkbox" id="is_public" name="is_public" value="1" 
                                   {{ old('is_public', true) ? 'checked' : '' }}
                                   style="margin-right: 10px;">
                            <label for="is_public" class="form-label" style="margin: 0; cursor: pointer;">
                                <i class="bi bi-globe"></i> {{ trans('dune-rp::messages.events.fields.is_public') }}
                            </label>
                        </div>
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.is_public') }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-coin"></i> {{ trans('dune-rp::messages.events.fields.economics') }}
                        </label>
                        <div class="cost-reward-inputs">
                            <div class="input-group">
                                <span class="input-group-text">{{ trans('dune-rp::messages.spice.cost') }}</span>
                                <input type="number" id="spice_cost" name="spice_cost" class="form-control" 
                                       value="{{ old('spice_cost', 0) }}" min="0" max="1000" step="0.01">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">{{ trans('dune-rp::messages.spice.reward') }}</span>
                                <input type="number" id="reward_spice" name="reward_spice" class="form-control" 
                                       value="{{ old('reward_spice', 0) }}" min="0" max="10000" step="0.01">
                            </div>
                        </div>
                        <div class="form-text">{{ trans('dune-rp::messages.events.help.economics') }}</div>
                    </div>
                </div>
            </div>

            {{-- Preview Panel --}}
            <div class="preview-panel" id="eventPreview" style="display: none;">
                <h4 style="color: var(--dune-spice-glow); margin-bottom: 15px;">
                    <i class="bi bi-eye"></i> {{ trans('dune-rp::messages.events.preview') }}
                </h4>
                <div id="previewContent"></div>
            </div>

            {{-- Form Actions --}}
            <div style="display: flex; gap: 15px; justify-content: center; align-items: center; margin-top: 30px; flex-wrap: wrap;">
                <button type="button" id="previewBtn" class="dune-button secondary">
                    <i class="bi bi-eye"></i> {{ trans('dune-rp::messages.events.preview') }}
                </button>
                
                <button type="submit" class="dune-button">
                    <i class="bi bi-calendar-plus"></i> {{ trans('dune-rp::messages.events.create') }}
                </button>
                
                <a href="{{ route('dune-rp.events.index') }}" class="dune-button secondary">
                    <i class="bi bi-arrow-left"></i> {{ trans('dune-rp::messages.events.cancel') }}
                </a>
            </div>
        </form>
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
        let preview = '<div style="background: rgba(15,15,35,0.7); padding: 20px; border-radius: 10px; border-left: 4px solid var(--dune-spice);">';
        
        // Title
        const title = formData.get('title') || 'Titre de l\'événement';
        preview += `<h3 style="color: var(--dune-spice-glow); margin-bottom: 15px;">${title}</h3>`;
        
        // Type
        if (eventTypeInput.value) {
            const selectedCard = document.querySelector(`[data-type="${eventTypeInput.value}"]`);
            if (selectedCard) {
                const typeName = selectedCard.querySelector('div').textContent;
                preview += `<div style="display: inline-block; background: var(--dune-spice); color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; margin-bottom: 10px;">${typeName}</div>`;
            }
        }
        
        // Date and location
        const eventDate = document.getElementById('event_date').value;
        const eventTime = document.getElementById('event_time').value;
        const location = formData.get('location');
        
        if (eventDate || location) {
            preview += '<div style="margin: 15px 0; display: flex; gap: 20px; flex-wrap: wrap;">';
            if (eventDate) {
                const dateObj = new Date(eventDate + 'T' + (eventTime || '20:00'));
                preview += `<div><i class="bi bi-calendar"></i> ${dateObj.toLocaleDateString('fr-FR')} à ${dateObj.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>`;
            }
            if (location) {
                preview += `<div><i class="bi bi-geo-alt"></i> ${location}</div>`;
            }
            preview += '</div>';
        }
        
        // Description
        const description = formData.get('description');
        if (description) {
            preview += `<div style="margin: 15px 0; line-height: 1.6; color: var(--dune-sand);">${description.replace(/\n/g, '<br>')}</div>`;
        }
        
        // Economics
        const spiceCost = formData.get('spice_cost');
        const rewardSpice = formData.get('reward_spice');
        if (spiceCost > 0 || rewardSpice > 0) {
            preview += '<div style="margin: 15px 0; display: flex; gap: 15px;">';
            if (spiceCost > 0) {
                preview += `<div style="color: var(--dune-spice-glow);"><i class="bi bi-coin"></i> Coût: ${spiceCost} Épice</div>`;
            }
            if (rewardSpice > 0) {
                preview += `<div style="color: var(--dune-spice-glow);"><i class="bi bi-trophy"></i> Récompense: ${rewardSpice} Épice</div>`;
            }
            preview += '</div>';
        }
        
        preview += '</div>';
        
        previewContent.innerHTML = preview;
        previewPanel.style.display = previewPanel.style.display === 'none' ? 'block' : 'none';
        
        if (previewPanel.style.display === 'block') {
            previewPanel.scrollIntoView({ behavior: 'smooth' });
        }
    });

    // Real-time character count for description
    const descriptionTextarea = document.getElementById('description');
    const maxLength = 5000;
    
    descriptionTextarea.addEventListener('input', function() {
        const current = this.value.length;
        const remaining = maxLength - current;
        
        // Find or create counter element
        let counter = document.getElementById('description-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.id = 'description-counter';
            counter.style.cssText = 'font-size: 0.8rem; color: var(--dune-sand-dark); margin-top: 5px; text-align: right;';
            this.parentNode.appendChild(counter);
        }
        
        counter.textContent = `${current}/${maxLength} caractères`;
        
        if (remaining < 100) {
            counter.style.color = '#f44336';
        } else {
            counter.style.color = 'var(--dune-sand-dark)';
        }
    });

    // Auto-save to localStorage (draft functionality)
    let autoSaveTimeout;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                const formData = new FormData(form);
                const draft = Object.fromEntries(formData.entries());
                localStorage.setItem('event_draft', JSON.stringify(draft));
                
                // Show saved indicator
                let indicator = document.getElementById('save-indicator');
                if (!indicator) {
                    indicator = document.createElement('div');
                    indicator.id = 'save-indicator';
                    indicator.style.cssText = 'position: fixed; top: 20px; right: 20px; background: var(--dune-spice); color: white; padding: 10px 15px; border-radius: 5px; z-index: 1000; opacity: 0; transition: opacity 0.3s;';
                    document.body.appendChild(indicator);
                }
                
                indicator.textContent = 'Brouillon sauvegardé';
                indicator.style.opacity = '1';
                
                setTimeout(() => {
                    indicator.style.opacity = '0';
                }, 2000);
            }, 1000);
        });
    });

    // Load draft on page load
    const savedDraft = localStorage.getItem('event_draft');
    if (savedDraft) {
        try {
            const draft = JSON.parse(savedDraft);
            
            // Ask user if they want to restore
            if (confirm('Un brouillon d\'événement a été trouvé. Voulez-vous le restaurer ?')) {
                Object.keys(draft).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = draft[key];
                        
                        // Handle checkboxes
                        if (input.type === 'checkbox') {
                            input.checked = draft[key] === '1';
                        }
                        
                        // Handle event type selection
                        if (key === 'event_type') {
                            const card = document.querySelector(`[data-type="${draft[key]}"]`);
                            if (card) {
                                typeCards.forEach(c => c.classList.remove('selected'));
                                card.classList.add('selected');
                            }
                        }
                    }
                });
            } else {
                localStorage.removeItem('event_draft');
            }
        } catch (e) {
            localStorage.removeItem('event_draft');
        }
    }
    
    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('event_draft');
    });
});
</script>
@endpush
```

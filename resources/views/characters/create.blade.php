@extends('layouts.app')

@section('title', trans('dune-rp::messages.characters.create'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="page-header dune-panel" style="text-align: center; padding: 40px 20px; background: linear-gradient(135deg, rgba(30,74,140,0.3), rgba(139,0,0,0.3));">
        <h1 class="dune-heading" style="font-size: 2.5rem; margin-bottom: 15px;">
            <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.characters.create') }}
        </h1>
        <p style="font-size: 1.1rem; color: var(--dune-sand); max-width: 700px; margin: 0 auto;">
            Donnez vie à votre légende dans l'univers de Dune. Chaque détail compte pour forger votre destinée.
        </p>
    </div>

    {{-- Character Creation Form --}}
    <div class="character-form-container" style="max-width: 900px; margin: 30px auto;">
        <form method="POST" action="{{ route('dune-rp.characters.store') }}" enctype="multipart/form-data" class="dune-form">
            @csrf
            
            <div class="form-sections" style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                
                {{-- Basic Information Section --}}
                <div class="form-section dune-panel" style="padding: 30px;">
                    <h2 class="section-title dune-heading" style="margin-bottom: 25px; color: var(--dune-spice-glow); font-size: 1.6rem;">
                        <i class="bi bi-person"></i> Informations Générales
                    </h2>
                    
                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        {{-- Character Name --}}
                        <div class="form-group">
                            <label for="name" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-person"></i> {{ trans('dune-rp::messages.characters.form.name') }} <span style="color: #f44336;">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="dune-input" 
                                   value="{{ old('name') }}" 
                                   placeholder="Nom de votre personnage"
                                   required minlength="2" maxlength="100">
                            @error('name')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Character Title --}}
                        <div class="form-group">
                            <label for="title" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-award"></i> {{ trans('dune-rp::messages.characters.form.title') }}
                            </label>
                            <input type="text" id="title" name="title" class="dune-input" 
                                   value="{{ old('title') }}" 
                                   placeholder="Baron, Dame, Sire..."
                                   maxlength="100">
                            @error('title')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Age --}}
                        <div class="form-group">
                            <label for="age" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-calendar"></i> {{ trans('dune-rp::messages.characters.age') }}
                            </label>
                            <input type="number" id="age" name="age" class="dune-input" 
                                   value="{{ old('age') }}" 
                                   placeholder="Âge en années"
                                   min="1" max="500">
                            @error('age')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Birthworld --}}
                        <div class="form-group">
                            <label for="birthworld" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-globe"></i> {{ trans('dune-rp::messages.characters.birthworld') }}
                            </label>
                            <input type="text" id="birthworld" name="birthworld" class="dune-input" 
                                   value="{{ old('birthworld') }}" 
                                   placeholder="Arrakis, Caladan, Giedi Prime..."
                                   maxlength="100">
                            @error('birthworld')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- House Selection --}}
                <div class="form-section dune-panel" style="padding: 30px;">
                    <h2 class="section-title dune-heading" style="margin-bottom: 25px; color: var(--dune-spice-glow); font-size: 1.6rem;">
                        <i class="bi bi-shield"></i> Affiliation
                    </h2>
                    
                    <div class="house-selection">
                        <label for="house_id" style="display: block; margin-bottom: 15px; color: var(--dune-sand); font-weight: bold;">
                            <i class="bi bi-shield"></i> {{ trans('dune-rp::messages.characters.form.select_house') }}
                        </label>
                        
                        @if($houses->count() > 0)
                            <div class="houses-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                                {{-- No House Option --}}
                                <div class="house-option">
                                    <input type="radio" id="house_none" name="house_id" value="" 
                                           {{ old('house_id') == '' ? 'checked' : '' }} style="display: none;">
                                    <label for="house_none" class="house-card" style="display: block; padding: 20px; border: 2px solid var(--dune-sand-dark); border-radius: 10px; cursor: pointer; text-align: center; transition: all 0.3s;">
                                        <div style="font-size: 2rem; margin-bottom: 10px; color: var(--dune-sand-dark);">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <h4 style="margin: 0 0 5px 0; color: var(--dune-sand);">Sans Maison</h4>
                                        <p style="margin: 0; font-size: 0.9rem; color: var(--dune-sand-dark);">Libre de toute alliance</p>
                                    </label>
                                </div>
                                
                                @foreach($houses as $house)
                                    <div class="house-option">
                                        <input type="radio" id="house_{{ $house->id }}" name="house_id" value="{{ $house->id }}" 
                                               {{ old('house_id') == $house->id ? 'checked' : '' }} style="display: none;">
                                        <label for="house_{{ $house->id }}" class="house-card" style="display: block; padding: 20px; border: 2px solid {{ $house->color ?? 'var(--dune-sand)' }}; border-radius: 10px; cursor: pointer; text-align: center; transition: all 0.3s;">
                                            @if($house->sigil_url)
                                                <div style="width: 60px; height: 60px; margin: 0 auto 15px; background-image: url('{{ $house->getImageUrl() }}'); background-size: cover; background-position: center; border-radius: 50%;"></div>
                                            @else
                                                <div style="font-size: 2.5rem; margin-bottom: 10px; color: {{ $house->color ?? 'var(--dune-sand)' }};">
                                                    <i class="bi bi-shield"></i>
                                                </div>
                                            @endif
                                            <h4 style="margin: 0 0 5px 0; color: {{ $house->color ?? 'var(--dune-spice-glow)' }};">{{ $house->name }}</h4>
                                            @if($house->motto)
                                                <p style="margin: 0; font-size: 0.8rem; color: var(--dune-sand); font-style: italic;">"{{ Str::limit($house->motto, 40) }}"</p>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-houses" style="text-align: center; padding: 30px; background: rgba(214,166,95,0.1); border-radius: 8px; border: 2px dashed var(--dune-sand);">
                                <i class="bi bi-shield-x" style="font-size: 3rem; color: var(--dune-sand-dark); margin-bottom: 15px;"></i>
                                <p style="color: var(--dune-sand); margin: 0;">Aucune maison disponible pour le moment.</p>
                            </div>
                        @endif
                        
                        @error('house_id')
                            <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 10px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Biography Section --}}
                <div class="form-section dune-panel" style="padding: 30px;">
                    <h2 class="section-title dune-heading" style="margin-bottom: 25px; color: var(--dune-spice-glow); font-size: 1.6rem;">
                        <i class="bi bi-book"></i> Histoire du Personnage
                    </h2>
                    
                    <div class="form-group">
                        <label for="biography" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                            <i class="bi bi-book"></i> {{ trans('dune-rp::messages.characters.biography') }}
                        </label>
                        <div style="margin-bottom: 10px; font-size: 0.9rem; color: var(--dune-sand-dark);">
                            {{ trans('dune-rp::messages.characters.form.biography_help') }}
                        </div>
                        <textarea id="biography" name="biography" class="dune-textarea" rows="8" maxlength="5000" placeholder="Racontez l'histoire de votre personnage, ses origines, ses motivations, ses expériences passées...">{{ old('biography') }}</textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                Markdown supporté (gras, italique, liens, etc.)
                            </div>
                            <div id="bioCounter" style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                <span id="bioLength">{{ strlen(old('biography', '')) }}</span>/5000 caractères
                            </div>
                        </div>
                        @error('biography')
                            <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Special Abilities Section --}}
                <div class="form-section dune-panel" style="padding: 30px;">
                    <h2 class="section-title dune-heading" style="margin-bottom: 25px; color: var(--dune-spice-glow); font-size: 1.6rem;">
                        <i class="bi bi-lightning"></i> Capacités Spéciales
                    </h2>
                    
                    <div style="margin-bottom: 20px; padding: 15px; background: rgba(255,215,0,0.1); border-radius: 8px; border-left: 4px solid var(--dune-spice-glow);">
                        <p style="margin: 0; color: var(--dune-sand); font-size: 0.95rem;">
                            <i class="bi bi-info-circle"></i> {{ trans('dune-rp::messages.characters.form.abilities_help') }}
                        </p>
                    </div>
                    
                    <div class="abilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        @foreach(\Azuriom\Plugin\DuneRp\Models\Character::SPECIAL_ABILITIES as $abilityKey => $abilityName)
                            <div class="ability-option">
                                <input type="checkbox" id="ability_{{ $abilityKey }}" name="special_abilities[]" value="{{ $abilityKey }}" 
                                       {{ in_array($abilityKey, old('special_abilities', [])) ? 'checked' : '' }} style="display: none;">
                                <label for="ability_{{ $abilityKey }}" class="ability-card" style="display: block; padding: 20px; border: 2px solid rgba(230,126,34,0.3); border-radius: 10px; cursor: pointer; transition: all 0.3s; background: rgba(230,126,34,0.05);">
                                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                        <i class="bi bi-lightning" style="font-size: 1.5rem; color: var(--dune-spice); margin-right: 10px;"></i>
                                        <h4 style="margin: 0; color: var(--dune-spice-glow);">{{ $abilityName }}</h4>
                                    </div>
                                    <p style="margin: 0; font-size: 0.9rem; color: var(--dune-sand); line-height: 1.4;">
                                        @switch($abilityKey)
                                            @case('prescience')
                                                Capacité à percevoir des bribes du futur et d'anticiper certains événements.
                                                @break
                                            @case('voice')
                                                Maîtrise de techniques vocales permettant d'influencer et de commander autrui.
                                                @break
                                            @case('mentat')
                                                Calculateur humain capable d'analyses complexes et de déductions logiques.
                                                @break
                                            @case('bene_gesserit')
                                                Maîtrise des techniques Bene Gesserit : combat, manipulation, contrôle corporel.
                                                @break
                                            @case('fremen_skills')
                                                Compétences de survie dans le désert et connaissance d'Arrakis.
                                                @break
                                            @case('spice_trance')
                                                Capacité à utiliser l'épice pour atteindre des états modifiés.
                                                @break
                                            @case('stillsuit_mastery')
                                                Expertise dans l'utilisation et l'entretien des distilles.
                                                @break
                                            @case('sandwalk')
                                                Technique de marche pour éviter d'attirer les vers des sables.
                                                @break
                                            @default
                                                Capacité spéciale unique développée par le personnage.
                                        @endswitch
                                    </p>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <div id="abilityCounter" style="text-align: center; margin-top: 15px; color: var(--dune-sand-dark); font-size: 0.9rem;">
                        <span id="selectedAbilities">0</span>/5 capacités sélectionnées
                    </div>
                    
                    @error('special_abilities')
                        <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 10px; text-align: center;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Avatar & Settings Section --}}
                <div class="form-section dune-panel" style="padding: 30px;">
                    <h2 class="section-title dune-heading" style="margin-bottom: 25px; color: var(--dune-spice-glow); font-size: 1.6rem;">
                        <i class="bi bi-image"></i> Avatar & Paramètres
                    </h2>
                    
                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        {{-- Avatar Upload --}}
                        <div class="form-group">
                            <label for="avatar" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-image"></i> {{ trans('dune-rp::messages.characters.form.avatar') }}
                            </label>
                            <div class="avatar-upload-area" style="border: 2px dashed var(--dune-sand); border-radius: 10px; padding: 30px; text-align: center; background: rgba(214,166,95,0.05);">
                                <div id="avatarPreview" style="width: 120px; height: 120px; margin: 0 auto 15px; border: 3px solid var(--dune-sand); border-radius: 50%; overflow: hidden; background: linear-gradient(45deg, var(--dune-sand), var(--dune-spice)); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera" style="font-size: 2.5rem; color: white;"></i>
                                </div>
                                <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                                <label for="avatar" class="dune-button secondary" style="cursor: pointer;">
                                    <i class="bi bi-upload"></i> Choisir une image
                                </label>
                                <div style="margin-top: 10px; font-size: 0.8rem; color: var(--dune-sand-dark);">
                                    JPG, PNG, GIF - Max 2MB
                                </div>
                            </div>
                            @error('avatar')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Privacy Settings --}}
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 15px; color: var(--dune-sand); font-weight: bold;">
                                <i class="bi bi-gear"></i> Paramètres de Confidentialité
                            </label>
                            
                            <div class="settings-list" style="display: flex; flex-direction: column; gap: 15px;">
                                <div class="setting-item" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background: rgba(30,74,140,0.1); border-radius: 8px; border: 1px solid var(--dune-blue-eyes);">
                                    <div>
                                        <h5 style="margin: 0 0 5px 0; color: var(--dune-spice-glow);">Profil Public</h5>
                                        <p style="margin: 0; font-size: 0.9rem; color: var(--dune-sand);">Votre personnage sera visible par tous les joueurs.</p>
                                    </div>
                                    <div class="toggle-switch" style="position: relative; width: 60px; height: 30px;">
                                        <input type="hidden" name="is_public" value="0">
                                        <input type="checkbox" id="is_public" name="is_public" value="1" 
                                               {{ old('is_public', true) ? 'checked' : '' }}
                                               style="opacity: 0; width: 0; height: 0;">
                                        <label for="is_public" class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 30px;"></label>
                                    </div>
                                </div>
                            </div>
                            
                            @error('is_public')
                                <div class="field-error" style="color: #f44336; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions dune-panel" style="padding: 25px; text-align: center; margin-top: 30px;">
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('dune-rp.index') }}" class="dune-button secondary" style="min-width: 150px;">
                        <i class="bi bi-arrow-left"></i> {{ trans('dune-rp::messages.common.cancel') }}
                    </a>
                    <button type="submit" class="dune-button" style="min-width: 200px;">
                        <i class="bi bi-check"></i> {{ trans('dune-rp::messages.characters.form.save') }}
                    </button>
                </div>
                
                <div style="margin-top: 20px; font-size: 0.9rem; color: var(--dune-sand-dark); max-width: 600px; margin-left: auto; margin-right: auto;">
                    <i class="bi bi-info-circle"></i> 
                    Votre personnage sera soumis à validation par un administrateur avant d'être activé.
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* House selection styles */
.house-option input[type="radio"]:checked + .house-card {
    border-color: var(--dune-spice-glow);
    background: rgba(255,215,0,0.1);
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(255,215,0,0.3);
}

.house-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(214, 166, 95, 0.3);
}

/* Ability selection styles */
.ability-option input[type="checkbox"]:checked + .ability-card {
    border-color: var(--dune-spice-glow);
    background: rgba(255,215,0,0.15);
    transform: scale(1.02);
}

.ability-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(230, 126, 34, 0.3);
}

/* Toggle switch styles */
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: var(--dune-spice);
}

input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

/* Responsive design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr !important;
    }
    
    .houses-grid {
        grid-template-columns: 1fr !important;
    }
    
    .abilities-grid {
        grid-template-columns: 1fr !important;
    }
    
    .form-actions > div {
        flex-direction: column !important;
        align-items: stretch !important;
    }
}
</style>

<script>
// Biography counter
document.getElementById('biography').addEventListener('input', function() {
    const length = this.value.length;
    document.getElementById('bioLength').textContent = length;
    
    if (length > 4500) {
        document.getElementById('bioCounter').style.color = '#f44336';
    } else if (length > 4000) {
        document.getElementById('bioCounter').style.color = '#ff9800';
    } else {
        document.getElementById('bioCounter').style.color = 'var(--dune-sand-dark)';
    }
});

// Ability counter and limit
function updateAbilityCounter() {
    const checked = document.querySelectorAll('input[name="special_abilities[]"]:checked').length;
    document.getElementById('selectedAbilities').textContent = checked;
    
    const counter = document.getElementById('abilityCounter');
    if (checked >= 5) {
        counter.style.color = '#f44336';
        // Disable unchecked abilities
        document.querySelectorAll('input[name="special_abilities[]"]:not(:checked)').forEach(input => {
            input.disabled = true;
            input.parentElement.style.opacity = '0.5';
        });
    } else {
        counter.style.color = 'var(--dune-sand-dark)';
        // Re-enable all abilities
        document.querySelectorAll('input[name="special_abilities[]"]').forEach(input => {
            input.disabled = false;
            input.parentElement.style.opacity = '1';
        });
    }
}

// Add event listeners to ability checkboxes
document.querySelectorAll('input[name="special_abilities[]"]').forEach(input => {
    input.addEventListener('change', updateAbilityCounter);
});

// Initialize counter
updateAbilityCounter();

// Avatar preview
function previewAvatar(input) {
    const preview = document.getElementById('avatarPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    if (name.length < 2) {
        e.preventDefault();
        alert('Le nom du personnage doit contenir au moins 2 caractères.');
        document.getElementById('name').focus();
        return false;
    }
    
    const selectedAbilities = document.querySelectorAll('input[name="special_abilities[]"]:checked').length;
    if (selectedAbilities > 5) {
        e.preventDefault();
        alert('Vous ne pouvez sélectionner que 5 capacités spéciales maximum.');
        return false;
    }
});

// Auto-save to localStorage (optional)
function autoSave() {
    const formData = {
        name: document.getElementById('name').value,
        title: document.getElementById('title').value,
        age: document.getElementById('age').value,
        birthworld: document.getElementById('birthworld').value,
        biography: document.getElementById('biography').value,
        house_id: document.querySelector('input[name="house_id"]:checked')?.value || '',
        special_abilities: Array.from(document.querySelectorAll('input[name="special_abilities[]"]:checked')).map(input => input.value),
        is_public: document.getElementById('is_public').checked
    };
    
    localStorage.setItem('dune_rp_character_draft', JSON.stringify(formData));
}

// Auto-save every 30 seconds
setInterval(autoSave, 30000);

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem('dune_rp_character_draft');
    if (draft && !{{ old() ? 'true' : 'false' }}) {
        try {
            const data = JSON.parse(draft);
            if (confirm('Un brouillon de personnage a été trouvé. Voulez-vous le charger ?')) {
                Object.keys(data).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        if (element.type === 'checkbox') {
                            element.checked = data[key];
                        } else {
                            element.value = data[key];
                        }
                    }
                });
                
                // Handle special abilities
                if (data.special_abilities) {
                    data.special_abilities.forEach(ability => {
                        const checkbox = document.getElementById(`ability_${ability}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
                
                // Update counters
                updateAbilityCounter();
                document.getElementById('biography').dispatchEvent(new Event('input'));
            }
        } catch (e) {
            console.log('Error loading draft:', e);
        }
    }
});
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

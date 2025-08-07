@extends('layouts.app')

@section('title', trans('dune-rp::messages.houses.recruitment.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="recruitment-header dune-panel" style="text-align: center; padding: 50px 20px; background: linear-gradient(135deg, rgba(139,0,0,0.3), rgba(30,74,140,0.3));">
        <h1 class="dune-heading" style="font-size: 2.8rem; margin-bottom: 20px;">
            <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.houses.recruitment.title') }}
        </h1>
        <p style="font-size: 1.2rem; color: var(--dune-sand); max-width: 800px; margin: 0 auto 30px;">
            {{ trans('dune-rp::messages.houses.recruitment.description') }}
        </p>
        
        @auth
            @if(!auth()->user()->characters()->exists())
                <div class="recruitment-cta" style="background: rgba(255,215,0,0.1); padding: 20px; border-radius: 10px; border: 2px solid var(--dune-spice-glow); margin: 0 auto; max-width: 600px;">
                    <h3 style="color: var(--dune-spice-glow); margin-bottom: 15px;">Vous n'avez pas encore de personnage !</h3>
                    <p style="margin-bottom: 20px;">Créez votre personnage pour pouvoir rejoindre une Grande Maison.</p>
                    <a href="{{ route('dune-rp.characters.create') }}" class="dune-button" style="font-size: 1.1rem;">
                        <i class="bi bi-person-plus"></i> Créer mon personnage
                    </a>
                </div>
            @elseif(auth()->user()->characters()->whereNotNull('house_id')->exists())
                <div class="current-house-info" style="background: rgba(76,175,80,0.1); padding: 20px; border-radius: 10px; border: 2px solid #4caf50; margin: 0 auto; max-width: 600px;">
                    @php $userHouse = auth()->user()->characters()->whereNotNull('house_id')->first()->house @endphp
                    <h3 style="color: #4caf50; margin-bottom: 15px;">Vous faites déjà partie d'une Maison !</h3>
                    <p style="margin-bottom: 20px;">Vous êtes membre de la <strong>{{ $userHouse->name }}</strong>.</p>
                    <a href="{{ route('dune-rp.houses.show', $userHouse) }}" class="dune-button secondary">
                        <i class="bi bi-shield"></i> Voir ma Maison
                    </a>
                </div>
            @endif
        @else
            <div class="guest-cta" style="background: rgba(30,74,140,0.2); padding: 20px; border-radius: 10px; border: 2px solid var(--dune-blue-eyes); margin: 0 auto; max-width: 600px;">
                <h3 style="color: var(--dune-blue-eyes); margin-bottom: 15px;">Rejoignez l'aventure !</h3>
                <p style="margin-bottom: 20px;">Créez votre compte pour commencer votre aventure dans l'univers de Dune.</p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="{{ route('register') }}" class="dune-button">
                        <i class="bi bi-person-plus"></i> S'inscrire
                    </a>
                    <a href="{{ route('login') }}" class="dune-button secondary">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                </div>
            </div>
        @endauth
    </div>

    {{-- Recruitment Stats --}}
    <div class="recruitment-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px;">
            <div class="stat-number spice-glow" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;">
                {{ $recruitingHouses->count() }}
            </div>
            <div class="stat-label">Maisons Actives</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px;">
            <div class="stat-number spice-glow" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;">
                {{ $recruitingHouses->sum('active_characters_count') }}
            </div>
            <div class="stat-label">Membres Totaux</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px;">
            <div class="stat-number spice-glow" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;">
                {{ number_format($recruitingHouses->avg('influence_points'), 0) }}
            </div>
            <div class="stat-label">Influence Moyenne</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px;">
            <div class="stat-number spice-glow" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;">
                {{ number_format($recruitingHouses->sum('spice_reserves'), 0) }}
            </div>
            <div class="stat-label">Épice Totale</div>
        </div>
    </div>

    {{-- House Selection Guide --}}
    <div class="selection-guide dune-panel" style="margin-bottom: 40px; padding: 30px;">
        <h2 class="dune-heading" style="text-align: center; margin-bottom: 30px;">
            <i class="bi bi-compass"></i> Comment Choisir Votre Maison
        </h2>
        
        <div class="guide-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
            <div class="guide-item" style="background: rgba(30,74,140,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid var(--dune-blue-eyes);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-people" style="font-size: 2.5rem; color: var(--dune-blue-eyes);"></i>
                </div>
                <h4 style="color: var(--dune-spice-glow); text-align: center; margin-bottom: 15px;">Communauté</h4>
                <p style="text-align: center; line-height: 1.6;">
                    Choisissez une Maison avec des membres actifs et une communauté soudée. 
                    L'entraide et la coopération sont essentielles pour prospérer dans l'univers de Dune.
                </p>
            </div>
            
            <div class="guide-item" style="background: rgba(230,126,34,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid var(--dune-spice);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-gem" style="font-size: 2.5rem; color: var(--dune-spice);"></i>
                </div>
                <h4 style="color: var(--dune-spice-glow); text-align: center; margin-bottom: 15px;">Influence</h4>
                <p style="text-align: center; line-height: 1.6;">
                    Les Maisons influentes ont plus de pouvoir politique et d'opportunités. 
                    Mais les nouvelles Maisons offrent plus de possibilités d'évolution rapide.
                </p>
            </div>
            
            <div class="guide-item" style="background: rgba(139,0,0,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid var(--dune-imperial);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-book" style="font-size: 2.5rem; color: var(--dune-imperial);"></i>
                </div>
                <h4 style="color: var(--dune-spice-glow); text-align: center; margin-bottom: 15px;">Philosophie</h4>
                <p style="text-align: center; line-height: 1.6;">
                    Chaque Maison a sa propre philosophie et ses objectifs. 
                    Lisez leurs descriptions pour trouver celle qui correspond à votre style de jeu.
                </p>
            </div>
        </div>
    </div>

    {{-- Recruiting Houses --}}
    @if($recruitingHouses->count() > 0)
        <div class="recruiting-houses">
            <h2 class="dune-heading" style="text-align: center; margin-bottom: 40px; font-size: 2.2rem;">
                <i class="bi bi-shield-check"></i> Maisons en Recrutement
            </h2>
            
            <div class="houses-recruitment-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px;">
                @foreach($recruitingHouses as $house)
                    <div class="recruitment-house-card dune-panel fade-in" style="padding: 30px; position: relative; border-color: {{ $house->color ?? 'var(--dune-sand)' }};">
                        {{-- House Header --}}
                        <div class="house-header" style="text-align: center; margin-bottom: 25px;">
                            <div class="house-sigil" style="width: 100px; height: 100px; margin: 0 auto 20px; border: 3px solid {{ $house->color ?? 'var(--dune-sand)' }}; border-radius: 50%; position: relative;">
                                @if($house->sigil_url)
                                    <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    <div style="width: 100%; height: 100%; background: linear-gradient(45deg, {{ $house->color ?? 'var(--dune-sand)' }}, var(--dune-spice)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="bi bi-shield" style="font-size: 2.5rem; color: white;"></i>
                                    </div>
                                @endif
                                
                                {{-- Recruitment badge --}}
                                <div class="recruitment-badge" style="position: absolute; top: -10px; right: -10px; background: #4caf50; color: white; padding: 4px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: bold;">
                                    ACTIF
                                </div>
                            </div>
                            
                            <h3 style="margin: 0 0 10px 0; color: {{ $house->color ?? 'var(--dune-spice-glow)' }}; font-size: 1.6rem;">
                                {{ $house->name }}
                            </h3>
                            
                            @if($house->motto)
                                <p style="margin: 0 0 15px 0; font-style: italic; color: var(--dune-sand); font-size: 1rem; border-bottom: 1px solid {{ $house->color ?? 'var(--dune-sand)' }}; padding-bottom: 15px;">
                                    "{{ $house->motto }}"
                                </p>
                            @endif
                            
                            @if($house->leader)
                                <div class="leader-info" style="background: rgba(0,0,0,0.2); padding: 8px 16px; border-radius: 20px; display: inline-block; border: 1px solid {{ $house->color ?? 'var(--dune-sand)' }};">
                                    <i class="bi bi-person-crown" style="color: {{ $house->color ?? 'var(--dune-spice-glow)' }};"></i>
                                    <strong>Chef:</strong> {{ $house->leader->name }}
                                </div>
                            @endif
                        </div>

                        {{-- House Description --}}
                        @if($house->description)
                            <div class="house-description" style="margin-bottom: 25px; padding: 20px; background: rgba({{ $house->color ? substr($house->color, 1) : '214,166,95' }}, 0.1); border-radius: 8px; border-left: 4px solid {{ $house->color ?? 'var(--dune-sand)' }};">
                                <div style="line-height: 1.6; font-size: 1rem;">
                                    {!! Str::limit(strip_tags($house->parseDescription()), 300) !!}
                                </div>
                                @if(strlen(strip_tags($house->parseDescription())) > 300)
                                    <div style="text-align: center; margin-top: 15px;">
                                        <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="font-size: 0.9rem; padding: 6px 12px;">
                                            Lire la suite...
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- House Stats for Recruitment --}}
                        <div class="recruitment-stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                            <div class="recruitment-stat" style="text-align: center; padding: 15px; background: rgba(30,74,140,0.1); border-radius: 8px; border: 1px solid var(--dune-blue-eyes);">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--dune-spice-glow); margin-bottom: 5px;">
                                    {{ $house->active_characters_count }}
                                </div>
                                <div style="font-size: 0.9rem; color: var(--dune-sand);">Membres</div>
                            </div>
                            
                            <div class="recruitment-stat" style="text-align: center; padding: 15px; background: rgba(230,126,34,0.1); border-radius: 8px; border: 1px solid var(--dune-spice);">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--dune-spice-glow); margin-bottom: 5px;">
                                    {{ number_format($house->influence_points) }}
                                </div>
                                <div style="font-size: 0.9rem; color: var(--dune-sand);">Influence</div>
                            </div>
                            
                            <div class="recruitment-stat" style="text-align: center; padding: 15px; background: rgba(214,166,95,0.1); border-radius: 8px; border: 1px solid var(--dune-sand);">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--dune-spice-glow); margin-bottom: 5px;">
                                    {{ $house->getInfluenceLevel() }}
                                </div>
                                <div style="font-size: 0.9rem; color: var(--dune-sand);">Rang</div>
                            </div>
                        </div>

                        {{-- Additional House Info --}}
                        <div class="house-additional-info" style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 0.9rem; color: var(--dune-sand);">
                            @if($house->homeworld)
                                <span>
                                    <i class="bi bi-globe" style="color: {{ $house->color ?? 'var(--dune-sand)' }};"></i>
                                    {{ $house->homeworld }}
                                </span>
                            @endif
                            
                            <span>
                                <i class="bi bi-calendar" style="color: {{ $house->color ?? 'var(--dune-sand)' }};"></i>
                                {{ $house->created_at->format('Y') }}
                            </span>
                        </div>

                        {{-- Recruitment Actions --}}
                        <div class="recruitment-actions" style="display: flex; gap: 12px;">
                            <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="flex: 1; text-align: center;">
                                <i class="bi bi-eye"></i> Détails
                            </a>
                            
                            @auth
                                @if(!auth()->user()->characters()->whereNotNull('house_id')->exists())
                                    <button class="dune-button recruitment-btn" style="flex: 2; position: relative;" 
                                            onclick="applyToHouse({{ $house->id }}, '{{ $house->name }}')"
                                            data-house-id="{{ $house->id }}">
                                        <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.houses.recruitment.apply') }}
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="dune-button" style="flex: 2; text-align: center;">
                                    <i class="bi bi-box-arrow-in-right"></i> Se connecter pour postuler
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="no-recruiting-houses dune-panel" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-shield-x" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 25px;"></i>
            <h3 style="color: var(--dune-sand); margin-bottom: 20px;">Aucune Maison en Recrutement</h3>
            <p style="color: var(--dune-sand); margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                Toutes les Maisons ont actuellement leurs rangs complets. Revenez plus tard ou consultez la liste complète des Maisons.
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('dune-rp.houses.index') }}" class="dune-button">
                    <i class="bi bi-shield"></i> Voir Toutes les Maisons
                </a>
                <a href="{{ route('dune-rp.index') }}" class="dune-button secondary">
                    <i class="bi bi-house"></i> Retour à l'Accueil
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Application Modal --}}
@auth
@if(!auth()->user()->characters()->whereNotNull('house_id')->exists())
<div id="applicationModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content dune-panel" style="max-width: 600px; width: 90%; padding: 40px;">
        <h3 class="dune-heading" style="text-align: center; margin-bottom: 25px;">
            <i class="bi bi-person-plus"></i> Candidature à la Maison
        </h3>
        
        <div id="applicationContent">
            <p style="text-align: center; margin-bottom: 25px; color: var(--dune-sand); font-size: 1.1rem;" id="applicationText"></p>
            
            <form id="applicationForm" onsubmit="submitApplication(event)" style="margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="application_message" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">Message de motivation (optionnel):</label>
                    <textarea id="application_message" name="message" class="dune-textarea" rows="4" 
                              placeholder="Expliquez pourquoi vous souhaitez rejoindre cette Maison..."></textarea>
                </div>
                
                <div class="character-info" style="background: rgba(30,74,140,0.1); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--dune-blue-eyes);">
                    <h5 style="color: var(--dune-spice-glow); margin-bottom: 15px;">
                        <i class="bi bi-person"></i> Votre Personnage
                    </h5>
                    @if(auth()->user()->characters()->exists())
                        @php $character = auth()->user()->characters()->first() @endphp
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="character-avatar" style="width: 60px; height: 60px;">
                                @if($character->avatar_url)
                                    <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; background: var(--dune-sand); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="bi bi-person" style="font-size: 1.5rem; color: var(--dune-space);"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <h6 style="margin: 0 0 5px 0; color: var(--dune-spice-glow);">{{ $character->name }}</h6>
                                @if($character->title)
                                    <p style="margin: 0 0 5px 0; font-style: italic; color: var(--dune-sand);">{{ $character->title }}</p>
                                @endif
                                <p style="margin: 0; font-size: 0.9rem; color: var(--dune-sand);">
                                    Status: {{ $character->getStatusName() }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="modal-actions" style="display: flex; gap: 15px; justify-content: end;">
                    <button type="button" onclick="closeApplicationModal()" class="dune-button secondary">
                        <i class="bi bi-x"></i> Annuler
                    </button>
                    <button type="submit" class="dune-button" id="submitApplicationBtn">
                        <i class="bi bi-send"></i> Envoyer la Candidature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

<style>
/* Animation staggered pour les cartes */
.recruitment-house-card:nth-child(1) { animation-delay: 0.1s; }
.recruitment-house-card:nth-child(2) { animation-delay: 0.2s; }
.recruitment-house-card:nth-child(3) { animation-delay: 0.3s; }
.recruitment-house-card:nth-child(4) { animation-delay: 0.4s; }

/* Hover effects pour les cartes de recrutement */
.recruitment-house-card {
    transition: all 0.3s ease;
}

.recruitment-house-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(214, 166, 95, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .houses-recruitment-grid {
        grid-template-columns: 1fr !important;
    }
    
    .guide-grid {
        grid-template-columns: 1fr !important;
    }
    
    .recruitment-stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .recruitment-actions {
        flex-direction: column !important;
    }
    
    .recruitment-header h1 {
        font-size: 2rem !important;
    }
    
    .recruitment-header p {
        font-size: 1rem !important;
    }
}

.modal.show {
    display: flex !important;
}
</style>

<script>
@auth
@if(!auth()->user()->characters()->whereNotNull('house_id')->exists())
let selectedHouseId = null;
let selectedHouseName = '';

function applyToHouse(houseId, houseName) {
    selectedHouseId = houseId;
    selectedHouseName = houseName;
    
    document.getElementById('applicationText').textContent = 
        `Vous êtes sur le point de postuler pour rejoindre la Maison ${houseName}. Votre candidature sera examinée par les dirigeants de la Maison.`;
    
    document.getElementById('applicationModal').classList.add('show');
    document.getElementById('application_message').focus();
}

function closeApplicationModal() {
    document.getElementById('applicationModal').classList.remove('show');
    selectedHouseId = null;
    selectedHouseName = '';
    document.getElementById('application_message').value = '';
}

async function submitApplication(event) {
    event.preventDefault();
    
    if (!selectedHouseId) return;
    
    const submitBtn = document.getElementById('submitApplicationBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';
    
    const message = document.getElementById('application_message').value;
    
    try {
        const response = await fetch(`/dune-rp/houses/${selectedHouseId}/apply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: message
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showNotification('success', data.message || 'Candidature envoyée avec succès !');
            
            // Update the button for this house
            const houseButton = document.querySelector(`[data-house-id="${selectedHouseId}"]`);
            if (houseButton) {
                houseButton.innerHTML = '<i class="bi bi-clock"></i> Candidature Envoyée';
                houseButton.disabled = true;
                houseButton.classList.remove('dune-button');
                houseButton.classList.add('dune-button', 'secondary');
            }
            
            closeApplicationModal();
        } else {
            showNotification('error', data.message || 'Une erreur est survenue lors de l\'envoi de votre candidature.');
        }
    } catch (error) {
        showNotification('error', 'Erreur de connexion. Veuillez réessayer.');
    }
    
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `dune-alert ${type}`;
    notification.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideInRight 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('applicationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApplicationModal();
    }
});

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
@endif
@endauth
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

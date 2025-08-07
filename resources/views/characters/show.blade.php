@extends('layouts.app')

@section('title', $character->name . ' - ' . trans('dune-rp::messages.characters.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
    @if($character->house)
        <style>
            .character-theme {
                --character-house-color: {{ $character->house->color ?? '#D6A65F' }};
            }
            .house-accent {
                color: var(--character-house-color) !important;
            }
            .house-border {
                border-color: var(--character-house-color) !important;
            }
        </style>
    @endif
@endpush

@section('content')
<div class="dune-container character-theme">
    {{-- Character Header --}}
    <div class="character-header dune-panel" style="padding: 40px 30px; background: linear-gradient(135deg, rgba(15,15,35,0.9), {{ $character->house ? $character->house->color.'40' : 'rgba(214,166,95,0.3)' }});">
        <div class="character-header-content" style="display: grid; grid-template-columns: auto 1fr auto; gap: 30px; align-items: center; max-width: 1200px; margin: 0 auto;">
            
            {{-- Character Avatar --}}
            <div class="character-avatar-large" style="position: relative;">
                <div style="width: 150px; height: 150px; border: 4px solid {{ $character->house ? $character->house->color : 'var(--dune-sand)' }}; border-radius: 50%; overflow: hidden; position: relative;">
                    @if($character->avatar_url)
                        <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; background: linear-gradient(45deg, var(--dune-sand), var(--dune-spice)); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person" style="font-size: 4rem; color: white;"></i>
                        </div>
                    @endif
                    
                    {{-- Blue Eyes Effect for Fremen --}}
                    @if($character->hasAbility('fremen_skills'))
                        <div class="blue-eyes-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 35% 35%, rgba(30,74,140,0.8) 3px, transparent 4px), radial-gradient(circle at 65% 35%, rgba(30,74,140,0.8) 3px, transparent 4px); pointer-events: none;"></div>
                    @endif
                </div>
                
                {{-- Status Badge --}}
                <div class="status-badge status-{{ $character->status }}" style="position: absolute; bottom: 5px; right: 5px; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; border: 2px solid white;">
                    {{ $character->getStatusName() }}
                </div>
            </div>
            
            {{-- Character Info --}}
            <div class="character-main-info">
                <h1 style="margin: 0 0 10px 0; color: var(--dune-spice-glow); font-size: 2.5rem; font-family: var(--font-dune);">
                    {{ $character->name }}
                </h1>
                
                @if($character->title)
                    <h2 style="margin: 0 0 15px 0; color: {{ $character->house ? $character->house->color : 'var(--dune-sand)' }}; font-size: 1.4rem; font-style: italic; font-weight: 300;">
                        {{ $character->title }}
                    </h2>
                @endif
                
                @if($character->house)
                    <div class="character-house-info" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 12px 20px; background: rgba(0,0,0,0.3); border-radius: 25px; border: 2px solid {{ $character->house->color }};">
                        @if($character->house->sigil_url)
                            <img src="{{ $character->house->getImageUrl() }}" alt="{{ $character->house->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        @endif
                        
                        <div>
                            <div style="font-size: 0.9rem; color: var(--dune-sand);">Maison</div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: {{ $character->house->color }};">
                                {{ $character->house->name }}
                            </div>
                        </div>
                        
                        <a href="{{ route('dune-rp.houses.show', $character->house) }}" class="dune-button secondary" style="padding: 6px 12px; font-size: 0.9rem;">
                            <i class="bi bi-shield"></i> Voir
                        </a>
                    </div>
                @endif
                
                <div class="character-meta" style="display: flex; gap: 25px; flex-wrap: wrap; color: var(--dune-sand); font-size: 1rem;">
                    @if($character->age)
                        <div>
                            <i class="bi bi-calendar" style="color: var(--dune-spice);"></i>
                            <strong>{{ $character->age }} ans</strong>
                        </div>
                    @endif
                    
                    @if($character->birthworld)
                        <div>
                            <i class="bi bi-globe" style="color: var(--dune-spice);"></i>
                            <strong>{{ $character->birthworld }}</strong>
                        </div>
                    @endif
                    
                    <div>
                        <i class="bi bi-person" style="color: var(--dune-spice);"></i>
                        <strong>{{ $character->user->name }}</strong>
                    </div>
                    
                    <div>
                        <i class="bi bi-clock" style="color: var(--dune-spice);"></i>
                        <strong>{{ $character->created_at->diffForHumans() }}</strong>
                    </div>
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="character-actions" style="display: flex; flex-direction: column; gap: 12px; min-width: 150px;">
                @auth
                    @if(auth()->user()->id === $character->user_id)
                        <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button" style="text-align: center;">
                            <i class="bi bi-pencil"></i> {{ trans('dune-rp::messages.characters.edit') }}
                        </a>
                    @endif
                @endauth
                
                <a href="{{ route('dune-rp.characters.index') }}" class="dune-button secondary" style="text-align: center;">
                    <i class="bi bi-arrow-left"></i> {{ trans('dune-rp::messages.common.back') }}
                </a>
                
                @if($character->house)
                    <a href="{{ route('dune-rp.characters.by-house', $character->house) }}" class="dune-button secondary" style="text-align: center;">
                        <i class="bi bi-people"></i> Autres Membres
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="character-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
        {{-- Main Content --}}
        <div class="character-main-content">
            {{-- Character Biography --}}
            @if($character->biography)
                <div class="character-biography dune-panel" style="padding: 30px; margin-bottom: 30px;">
                    <h2 class="dune-heading house-accent" style="margin-bottom: 25px; font-size: 1.8rem;">
                        <i class="bi bi-book"></i> {{ trans('dune-rp::messages.characters.biography') }}
                    </h2>
                    
                    <div class="biography-content" style="line-height: 1.8; font-size: 1.1rem; color: var(--dune-sand);">
                        {!! $character->parseBiography() !!}
                    </div>
                </div>
            @endif

            {{-- Character Abilities --}}
            @if($character->special_abilities && count($character->special_abilities) > 0)
                <div class="character-abilities dune-panel" style="padding: 30px; margin-bottom: 30px;">
                    <h2 class="dune-heading house-accent" style="margin-bottom: 25px; font-size: 1.8rem;">
                        <i class="bi bi-lightning"></i> {{ trans('dune-rp::messages.characters.abilities') }}
                    </h2>
                    
                    <div class="abilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        @foreach($character->special_abilities as $abilityKey)
                            @php
                                $abilityName = \Azuriom\Plugin\DuneRp\Models\Character::SPECIAL_ABILITIES[$abilityKey] ?? $abilityKey;
                                $abilityDescriptions = [
                                    'prescience' => 'Capacité à percevoir des bribes du futur, permettant d\'anticiper certains événements.',
                                    'voice' => 'Maîtrise de techniques de contrôle vocal permettant d\'influencer et de commander autrui.',
                                    'mentat' => 'Calculateur humain capable d\'analyses complexes et de déductions logiques extraordinaires.',
                                    'bene_gesserit' => 'Maîtrise des techniques de la Bene Gesserit: combat, manipulation, contrôle corporel.',
                                    'fremen_skills' => 'Compétences de survie dans le désert et connaissance approfondie d\'Arrakis.',
                                    'spice_trance' => 'Capacité à utiliser l\'épice pour atteindre des états de conscience modifiés.',
                                    'stillsuit_mastery' => 'Expertise dans l\'utilisation et l\'entretien des distilles de survie.',
                                    'sandwalk' => 'Technique de marche permettant d\'éviter d\'attirer les vers des sables.'
                                ];
                                $abilityDescription = $abilityDescriptions[$abilityKey] ?? 'Capacité spéciale développée par le personnage.';
                            @endphp
                            
                            <div class="ability-card" style="background: linear-gradient(135deg, rgba(230,126,34,0.1), rgba(255,215,0,0.1)); padding: 20px; border-radius: 10px; border-left: 4px solid var(--dune-spice); position: relative; overflow: hidden;">
                                <div class="ability-icon" style="position: absolute; top: 15px; right: 15px; font-size: 2rem; color: var(--dune-spice-glow); opacity: 0.3;">
                                    @switch($abilityKey)
                                        @case('prescience')
                                            <i class="bi bi-eye"></i>
                                            @break
                                        @case('voice')
                                            <i class="bi bi-mic"></i>
                                            @break
                                        @case('mentat')
                                            <i class="bi bi-cpu"></i>
                                            @break
                                        @case('bene_gesserit')
                                            <i class="bi bi-person-arms-up"></i>
                                            @break
                                        @case('fremen_skills')
                                            <i class="bi bi-sun"></i>
                                            @break
                                        @case('spice_trance')
                                            <i class="bi bi-stars"></i>
                                            @break
                                        @case('stillsuit_mastery')
                                            <i class="bi bi-droplet"></i>
                                            @break
                                        @case('sandwalk')
                                            <i class="bi bi-footprints"></i>
                                            @break
                                        @default
                                            <i class="bi bi-lightning"></i>
                                    @endswitch
                                </div>
                                
                                <h4 style="margin: 0 0 15px 0; color: var(--dune-spice-glow); font-size: 1.3rem;">
                                    {{ $abilityName }}
                                </h4>
                                
                                <p style="margin: 0; line-height: 1.6; color: var(--dune-sand); font-size: 0.95rem;">
                                    {{ $abilityDescription }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Character Statistics --}}
            <div class="character-stats dune-panel" style="padding: 30px;">
                <h2 class="dune-heading house-accent" style="margin-bottom: 25px; font-size: 1.8rem;">
                    <i class="bi bi-bar-chart"></i> Profil Détaillé
                </h2>
                
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div class="stat-card" style="background: rgba(30,74,140,0.1); padding: 20px; border-radius: 10px; text-align: center; border: 2px solid var(--dune-blue-eyes);">
                        <div class="stat-icon" style="font-size: 2.5rem; color: var(--dune-blue-eyes); margin-bottom: 10px;">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="stat-label" style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Statut</div>
                        <div class="stat-value status-{{ $character->status }}" style="font-size: 1.2rem; font-weight: bold;">
                            {{ $character->getStatusName() }}
                        </div>
                    </div>
                    
                    <div class="stat-card" style="background: rgba(230,126,34,0.1); padding: 20px; border-radius: 10px; text-align: center; border: 2px solid var(--dune-spice);">
                        <div class="stat-icon" style="font-size: 2.5rem; color: var(--dune-spice); margin-bottom: 10px;">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div class="stat-label" style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Addiction à l'Épice</div>
                        <div class="stat-value" style="font-size: 1.2rem; font-weight: bold; color: var(--dune-spice-glow);">
                            {{ $character->getAddictionLevelName() }}
                        </div>
                    </div>
                    
                    @if($character->special_abilities)
                        <div class="stat-card" style="background: rgba(214,166,95,0.1); padding: 20px; border-radius: 10px; text-align: center; border: 2px solid var(--dune-sand);">
                            <div class="stat-icon" style="font-size: 2.5rem; color: var(--dune-sand); margin-bottom: 10px;">
                                <i class="bi bi-stars"></i>
                            </div>
                            <div class="stat-label" style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Capacités</div>
                            <div class="stat-value" style="font-size: 1.2rem; font-weight: bold; color: var(--dune-spice-glow);">
                                {{ count($character->special_abilities) }}
                            </div>
                        </div>
                    @endif
                    
                    @if($character->house)
                        <div class="stat-card house-border" style="background: rgba({{ substr($character->house->color ?? 'D6A65F', 1) }}, 0.1); padding: 20px; border-radius: 10px; text-align: center; border: 2px solid {{ $character->house->color }};">
                            <div class="stat-icon house-accent" style="font-size: 2.5rem; margin-bottom: 10px;">
                                <i class="bi bi-shield"></i>
                            </div>
                            <div class="stat-label" style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Rang dans la Maison</div>
                            <div class="stat-value house-accent" style="font-size: 1.2rem; font-weight: bold;">
                                {{ $character->isHouseLeader() ? 'Chef' : 'Membre' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="character-sidebar">
            {{-- Character Details Card --}}
            <div class="character-details-card dune-panel" style="padding: 25px; margin-bottom: 25px;">
                <h3 class="dune-heading house-accent" style="margin-bottom: 20px; font-size: 1.4rem;">
                    <i class="bi bi-info-circle"></i> Informations
                </h3>
                
                <div class="details-list" style="display: flex; flex-direction: column; gap: 15px;">
                    @if($character->age)
                        <div class="detail-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                            <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-calendar"></i> Âge
                            </span>
                            <span class="house-accent" style="font-weight: bold;">{{ $character->age }} ans</span>
                        </div>
                    @endif
                    
                    @if($character->birthworld)
                        <div class="detail-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                            <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-globe"></i> Monde Natal
                            </span>
                            <span class="house-accent" style="font-weight: bold;">{{ $character->birthworld }}</span>
                        </div>
                    @endif
                    
                    <div class="detail-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-person"></i> Joueur
                        </span>
                        <span class="house-accent" style="font-weight: bold;">{{ $character->user->name }}</span>
                    </div>
                    
                    <div class="detail-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-calendar-plus"></i> Créé
                        </span>
                        <span class="house-accent" style="font-weight: bold;">{{ $character->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="detail-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-eye"></i> Profil
                        </span>
                        <span style="color: {{ $character->is_public ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                            {{ $character->is_public ? 'Public' : 'Privé' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Spice Addiction Level --}}
            @if($character->spice_addiction_level > 0)
                <div class="addiction-card dune-panel" style="padding: 25px; margin-bottom: 25px; background: linear-gradient(135deg, rgba(230,126,34,0.1), rgba(255,215,0,0.1));">
                    <h3 class="dune-heading" style="margin-bottom: 20px; font-size: 1.4rem; color: var(--dune-spice-glow);">
                        <i class="bi bi-lightning-charge"></i> Addiction à l'Épice
                    </h3>
                    
                    <div class="addiction-level-display" style="text-align: center; margin-bottom: 20px;">
                        <div class="addiction-value" style="font-size: 2rem; font-weight: bold; color: var(--dune-spice-glow); margin-bottom: 10px;">
                            {{ $character->getAddictionLevelName() }}
                        </div>
                        <div style="font-size: 0.9rem; color: var(--dune-sand);">
                            Niveau {{ $character->spice_addiction_level }}/4
                        </div>
                    </div>
                    
                    <div class="addiction-bar" style="background: rgba(44, 24, 16, 0.8); height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
                        <div class="addiction-fill" style="height: 100%; background: linear-gradient(90deg, var(--dune-spice), var(--dune-spice-glow)); width: {{ ($character->spice_addiction_level / 4) * 100 }}%; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); position: relative;">
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%); animation: shimmer 2s infinite;"></div>
                        </div>
                    </div>
                    
                    <p style="font-size: 0.9rem; color: var(--dune-sand); text-align: center; line-height: 1.5; margin: 0;">
                        @switch($character->spice_addiction_level)
                            @case(1)
                                Exposition légère à l'épice. Effets mineurs sur la perception.
                                @break
                            @case(2)
                                Dépendance modérée. Changements notables dans les yeux.
                                @break
                            @case(3)
                                Forte addiction. Yeux complètement bleus, prescience naissante.
                                @break
                            @case(4)
                                Addiction critique. Risque de mort sans épice régulière.
                                @break
                            @default
                                État d'addiction inconnu.
                        @endswitch
                    </p>
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="quick-actions-card dune-panel" style="padding: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--dune-spice-glow); font-size: 1.2rem;">
                    Actions Rapides
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if($character->house)
                        <a href="{{ route('dune-rp.houses.show', $character->house) }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                            <i class="bi bi-shield"></i> Voir la Maison
                        </a>
                        
                        <a href="{{ route('dune-rp.characters.by-house', $character->house) }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                            <i class="bi bi-people"></i> Autres Membres
                        </a>
                    @endif
                    
                    <a href="{{ route('dune-rp.characters.gallery') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-images"></i> Galerie
                    </a>
                    
                    <a href="{{ route('dune-rp.characters.index') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-list"></i> Tous les Personnages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Status colors */
.status-alive { color: #4caf50; }
.status-missing { color: #ff9800; }
.status-deceased { color: #f44336; }
.status-exiled { color: #9c27b0; }

/* Badge status colors */
.status-badge.status-alive { background: #4caf50; }
.status-badge.status-missing { background: #ff9800; }
.status-badge.status-deceased { background: #f44336; }
.status-badge.status-exiled { background: #9c27b0; }

/* Responsive Design */
@media (max-width: 1024px) {
    .character-header-content {
        grid-template-columns: 1fr !important;
        text-align: center !important;
    }
    
    .character-content {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    .character-actions {
        min-width: auto !important;
        flex-direction: row !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
}

@media (max-width: 768px) {
    .character-header {
        padding: 30px 20px !important;
    }
    
    .character-header h1 {
        font-size: 2rem !important;
    }
    
    .character-meta {
        justify-content: center !important;
        gap: 15px !important;
    }
    
    .abilities-grid {
        grid-template-columns: 1fr !important;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
    }
}

/* Animations */
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Hover effects */
.ability-card {
    transition: all 0.3s ease;
}

.ability-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(230, 126, 34, 0.3);
}

.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(214, 166, 95, 0.2);
}
</style>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

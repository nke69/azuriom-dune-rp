@extends('layouts.app')

@section('title', trans('dune-rp::messages.characters.my_character'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
    @if($character->house)
        <style>
            .my-character-theme {
                --my-house-color: {{ $character->house->color ?? '#D6A65F' }};
            }
        </style>
    @endif
@endpush

@section('content')
<div class="dune-container my-character-theme">
    {{-- Character Status Banner --}}
    @if(!$character->is_approved)
        <div class="status-banner dune-alert warning" style="margin-bottom: 30px; text-align: center; font-size: 1.1rem;">
            <i class="bi bi-hourglass-split"></i>
            <strong>{{ trans('dune-rp::messages.characters.pending_approval') }}</strong>
            - Votre personnage est en attente d'approbation par un administrateur.
        </div>
    @elseif($character->status !== 'alive')
        <div class="status-banner dune-alert {{ $character->status === 'deceased' ? 'error' : 'warning' }}" style="margin-bottom: 30px; text-align: center; font-size: 1.1rem;">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Statut:</strong> {{ $character->getStatusName() }}
        </div>
    @endif

    {{-- Character Dashboard Header --}}
    <div class="character-dashboard-header dune-panel" style="padding: 40px 30px; background: linear-gradient(135deg, rgba(15,15,35,0.9), {{ $character->house ? $character->house->color.'40' : 'rgba(214,166,95,0.3)' }});">
        <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 30px; align-items: center; max-width: 1200px; margin: 0 auto;">
            
            {{-- Character Avatar --}}
            <div class="character-avatar-section">
                <div style="width: 120px; height: 120px; border: 3px solid {{ $character->house ? $character->house->color : 'var(--dune-sand)' }}; border-radius: 50%; overflow: hidden; position: relative; margin-bottom: 15px;">
                    @if($character->avatar_url)
                        <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; background: linear-gradient(45deg, var(--dune-sand), var(--dune-spice)); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person" style="font-size: 3.5rem; color: white;"></i>
                        </div>
                    @endif
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button secondary" style="font-size: 0.9rem; padding: 6px 12px;">
                        <i class="bi bi-camera"></i> Changer
                    </a>
                </div>
            </div>
            
            {{-- Character Info --}}
            <div class="character-info-section">
                <h1 style="margin: 0 0 10px 0; color: var(--dune-spice-glow); font-size: 2.2rem;">
                    {{ $character->name }}
                </h1>
                
                @if($character->title)
                    <h2 style="margin: 0 0 15px 0; color: {{ $character->house ? $character->house->color : 'var(--dune-sand)' }}; font-size: 1.2rem; font-style: italic;">
                        {{ $character->title }}
                    </h2>
                @endif
                
                @if($character->house)
                    <div class="house-info" style="background: rgba(0,0,0,0.3); padding: 12px 18px; border-radius: 20px; border: 2px solid {{ $character->house->color }}; display: inline-block; margin-bottom: 15px;">
                        <i class="bi bi-shield" style="color: {{ $character->house->color }};"></i>
                        <strong style="color: {{ $character->house->color }};">{{ $character->house->name }}</strong>
                        @if($character->isHouseLeader())
                            <span style="color: var(--dune-spice-glow); margin-left: 8px;">
                                <i class="bi bi-crown"></i> Chef
                            </span>
                        @endif
                    </div>
                @endif
                
                <div class="character-status-badges" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <span class="status-badge status-{{ $character->status }}" style="padding: 6px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">
                        {{ $character->getStatusName() }}
                    </span>
                    
                    <span class="approval-badge {{ $character->is_approved ? 'approved' : 'pending' }}" style="padding: 6px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; background: {{ $character->is_approved ? '#4caf50' : '#ff9800' }}; color: white;">
                        {{ $character->is_approved ? 'Approuvé' : 'En Attente' }}
                    </span>
                    
                    <span class="visibility-badge {{ $character->is_public ? 'public' : 'private' }}" style="padding: 6px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; background: {{ $character->is_public ? '#2196f3' : '#757575' }}; color: white;">
                        {{ $character->is_public ? 'Public' : 'Privé' }}
                    </span>
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="quick-actions-section" style="display: flex; flex-direction: column; gap: 12px; min-width: 150px;">
                <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button" style="text-align: center;">
                    <i class="bi bi-pencil"></i> {{ trans('dune-rp::messages.characters.edit') }}
                </a>
                
                @if($character->is_approved && $character->is_public)
                    <a href="{{ route('dune-rp.characters.show', $character) }}" class="dune-button secondary" style="text-align: center;">
                        <i class="bi bi-eye"></i> Profil Public
                    </a>
                @endif
                
                @if($character->house)
                    <a href="{{ route('dune-rp.houses.show', $character->house) }}" class="dune-button secondary" style="text-align: center;">
                        <i class="bi bi-shield"></i> Ma Maison
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Character Dashboard Content --}}
    <div class="character-dashboard-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
        
        {{-- Main Dashboard --}}
        <div class="main-dashboard">
            {{-- Character Progress Overview --}}
            <div class="progress-overview dune-panel" style="padding: 30px; margin-bottom: 30px;">
                <h2 class="dune-heading" style="margin-bottom: 25px; color: {{ $character->house ? $character->house->color : 'var(--dune-spice-glow)' }};">
                    <i class="bi bi-graph-up"></i> Aperçu du Personnage
                </h2>
                
                <div class="progress-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    {{-- Profile Completion --}}
                    @php
                        $completion = 0;
                        $completion += $character->name ? 20 : 0;
                        $completion += $character->biography ? 30 : 0;
                        $completion += $character->avatar_url ? 20 : 0;
                        $completion += $character->house_id ? 20 : 0;
                        $completion += ($character->age && $character->birthworld) ? 10 : 0;
                    @endphp
                    
                    <div class="progress-card" style="background: rgba(30,74,140,0.1); padding: 20px; border-radius: 10px; border: 2px solid var(--dune-blue-eyes);">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <i class="bi bi-person-check" style="font-size: 2.5rem; color: var(--dune-blue-eyes);"></i>
                        </div>
                        <h4 style="text-align: center; margin-bottom: 10px; color: var(--dune-spice-glow);">Profil</h4>
                        <div class="progress-bar" style="background: rgba(44, 24, 16, 0.8); height: 8px; border-radius: 4px; margin-bottom: 8px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, var(--dune-blue-eyes), #4fc3f7); width: {{ $completion }}%; transition: width 0.8s;"></div>
                        </div>
                        <div style="text-align: center; font-size: 1.2rem; font-weight: bold; color: var(--dune-spice-glow);">{{ $completion }}%</div>
                    </div>
                    
                    {{-- House Integration --}}
                    <div class="progress-card" style="background: rgba(230,126,34,0.1); padding: 20px; border-radius: 10px; border: 2px solid var(--dune-spice);">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <i class="bi bi-shield" style="font-size: 2.5rem; color: var(--dune-spice);"></i>
                        </div>
                        <h4 style="text-align: center; margin-bottom: 10px; color: var(--dune-spice-glow);">Maison</h4>
                        <div style="text-align: center;">
                            @if($character->house)
                                <div style="font-size: 1.2rem; font-weight: bold; color: {{ $character->house->color }}; margin-bottom: 5px;">
                                    {{ $character->house->name }}
                                </div>
                                <div style="font-size: 0.9rem; color: var(--dune-sand);">
                                    {{ $character->isHouseLeader() ? 'Chef de Maison' : 'Membre' }}
                                </div>
                            @else
                                <div style="font-size: 1rem; color: var(--dune-sand-dark); font-style: italic;">
                                    Sans Maison
                                </div>
                                <a href="{{ route('dune-rp.houses.recruitment') }}" class="dune-button secondary" style="font-size: 0.8rem; padding: 4px 8px; margin-top: 8px;">
                                    Rejoindre
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Character Development --}}
                    <div class="progress-card" style="background: rgba(214,166,95,0.1); padding: 20px; border-radius: 10px; border: 2px solid var(--dune-sand);">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <i class="bi bi-lightning" style="font-size: 2.5rem; color: var(--dune-sand);"></i>
                        </div>
                        <h4 style="text-align: center; margin-bottom: 10px; color: var(--dune-spice-glow);">Capacités</h4>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--dune-spice-glow); margin-bottom: 5px;">
                                {{ $character->special_abilities ? count($character->special_abilities) : 0 }}/5
                            </div>
                            <div style="font-size: 0.9rem; color: var(--dune-sand);">
                                Capacités Spéciales
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Character Biography (Editable Preview) --}}
            <div class="biography-section dune-panel" style="padding: 30px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="dune-heading" style="margin: 0; color: {{ $character->house ? $character->house->color : 'var(--dune-spice-glow)' }};">
                        <i class="bi bi-book"></i> {{ trans('dune-rp::messages.characters.biography') }}
                    </h2>
                    <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button secondary" style="font-size: 0.9rem;">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
                
                @if($character->biography)
                    <div class="biography-content" style="line-height: 1.8; font-size: 1.1rem; color: var(--dune-sand);">
                        {!! $character->parseBiography() !!}
                    </div>
                @else
                    <div class="empty-biography" style="text-align: center; padding: 40px; background: rgba(214,166,95,0.1); border-radius: 8px; border: 2px dashed var(--dune-sand);">
                        <i class="bi bi-book" style="font-size: 3rem; color: var(--dune-sand-dark); margin-bottom: 15px;"></i>
                        <h4 style="color: var(--dune-sand); margin-bottom: 15px;">Biographie Manquante</h4>
                        <p style="color: var(--dune-sand); margin-bottom: 20px;">
                            Racontez l'histoire de votre personnage pour enrichir l'expérience RP.
                        </p>
                        <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button">
                            <i class="bi bi-pencil"></i> Ajouter une Biographie
                        </a>
                    </div>
                @endif
            </div>

            {{-- Character Abilities --}}
            @if($character->special_abilities && count($character->special_abilities) > 0)
                <div class="abilities-section dune-panel" style="padding: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 class="dune-heading" style="margin: 0; color: {{ $character->house ? $character->house->color : 'var(--dune-spice-glow)' }};">
                            <i class="bi bi-lightning"></i> Capacités Spéciales
                        </h2>
                        <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button secondary" style="font-size: 0.9rem;">
                            <i class="bi bi-gear"></i> Gérer
                        </a>
                    </div>
                    
                    <div class="abilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        @foreach($character->getAbilitiesNames() as $index => $abilityName)
                            <div class="ability-card" style="background: linear-gradient(135deg, rgba(230,126,34,0.1), rgba(255,215,0,0.1)); padding: 15px; border-radius: 8px; border-left: 4px solid var(--dune-spice);">
                                <h5 style="margin: 0 0 8px 0; color: var(--dune-spice-glow);">{{ $abilityName }}</h5>
                                <div style="font-size: 0.8rem; color: var(--dune-sand);">
                                    Capacité {{ $index + 1 }}/{{ count($character->special_abilities) }}
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- Add ability slots if less than 5 --}}
                        @for($i = count($character->special_abilities); $i < 5; $i++)
                            <div class="empty-ability-slot" style="background: rgba(44,24,16,0.3); padding: 15px; border-radius: 8px; border: 2px dashed var(--dune-sand-dark); text-align: center;">
                                <i class="bi bi-plus" style="font-size: 1.5rem; color: var(--dune-sand-dark); margin-bottom: 8px;"></i>
                                <div style="font-size: 0.9rem; color: var(--dune-sand-dark);">Emplacement Libre</div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="character-sidebar">
            {{-- Quick Stats --}}
            <div class="quick-stats dune-panel" style="padding: 25px; margin-bottom: 25px;">
                <h3 class="dune-heading" style="margin-bottom: 20px; font-size: 1.4rem; color: {{ $character->house ? $character->house->color : 'var(--dune-spice-glow)' }};">
                    <i class="bi bi-speedometer2"></i> Statistiques
                </h3>
                
                <div class="stats-list" style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(30,74,140,0.1); border-radius: 8px;">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-calendar"></i> Âge
                        </span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">
                            {{ $character->age ?? 'Non défini' }}{{ $character->age ? ' ans' : '' }}
                        </span>
                    </div>
                    
                    <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(230,126,34,0.1); border-radius: 8px;">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-lightning-charge"></i> Addiction
                        </span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">
                            {{ $character->getAddictionLevelName() }}
                        </span>
                    </div>
                    
                    <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(214,166,95,0.1); border-radius: 8px;">
                        <span style="color: var(--dune-sand); display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-globe"></i> Monde
                        </span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">
                            {{ $character->birthworld ?? 'Non défini' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- House Dashboard (if member) --}}
            @if($character->house)
                <div class="house-dashboard dune-panel" style="padding: 25px; margin-bottom: 25px; border: 2px solid {{ $character->house->color }};">
                    <h3 class="dune-heading" style="margin-bottom: 20px; font-size: 1.4rem; color: {{ $character->house->color }};">
                        <i class="bi bi-shield"></i> Tableau de Bord - {{ $character->house->name }}
                    </h3>
                    
                    <div class="house-quick-info" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--dune-sand);">Influence:</span>
                            <span style="color: {{ $character->house->color }}; font-weight: bold;">
                                {{ number_format($character->house->influence_points) }}
                            </span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--dune-sand);">Épice:</span>
                            <span class="spice-glow" style="font-weight: bold;">
                                {{ number_format($character->house->spice_reserves, 0) }}
                            </span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--dune-sand);">Membres:</span>
                            <span style="color: {{ $character->house->color }}; font-weight: bold;">
                                {{ $character->house->getActiveMembersCount() }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('dune-rp.houses.show', $character->house) }}" class="dune-button secondary" style="flex: 1; text-align: center; font-size: 0.9rem;">
                            <i class="bi bi-eye"></i> Voir
                        </a>
                        @if($character->isHouseLeader())
                            <a href="#" class="dune-button" style="flex: 1; text-align: center; font-size: 0.9rem;" onclick="alert('Fonctionnalité en développement')">
                                <i class="bi bi-gear"></i> Gérer
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Action Center --}}
            <div class="action-center dune-panel" style="padding: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--dune-spice-glow); font-size: 1.2rem;">
                    <i class="bi bi-lightning-fill"></i> Actions Rapides
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('dune-rp.characters.edit') }}" class="dune-button" style="width: 100%; text-align: center;">
                        <i class="bi bi-pencil"></i> Modifier Profil
                    </a>
                    
                    @if(!$character->house)
                        <a href="{{ route('dune-rp.houses.recruitment') }}" class="dune-button" style="width: 100%; text-align: center;">
                            <i class="bi bi-shield-plus"></i> Rejoindre une Maison
                        </a>
                    @endif
                    
                    <a href="{{ route('dune-rp.events.index') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-calendar-event"></i> Événements RP
                    </a>
                    
                    <a href="{{ route('dune-rp.characters.gallery') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-images"></i> Galerie
                    </a>
                </div>
            </div>

            {{-- Tips & Hints --}}
            <div class="tips-section dune-panel" style="padding: 20px; background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(230,126,34,0.1));">
                <h4 style="margin: 0 0 15px 0; color: var(--dune-spice-glow); font-size: 1.1rem;">
                    <i class="bi bi-lightbulb"></i> Conseils RP
                </h4>
                
                <div style="font-size: 0.9rem; color: var(--dune-sand); line-height: 1.6;">
                    @php
                        $tips = [
                            'Développez une biographie riche pour enrichir vos interactions RP.',
                            'Choisissez vos capacités spéciales en fonction de l\'histoire de votre personnage.',
                            'Participez aux événements de votre Maison pour gagner en influence.',
                            'L\'addiction à l\'épice peut offrir des capacités mais aussi des risques.',
                            'Entretenez de bonnes relations avec les autres membres de votre Maison.'
                        ];
                        $randomTip = $tips[array_rand($tips)];
                    @endphp
                    
                    <p style="margin: 0;">{{ $randomTip }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Status badge colors */
.status-badge.status-alive { background: #4caf50; }
.status-badge.status-missing { background: #ff9800; }
.status-badge.status-deceased { background: #f44336; }
.status-badge.status-exiled { background: #9c27b0; }

/* Responsive */
@media (max-width: 1024px) {
    .character-dashboard-header > div {
        grid-template-columns: 1fr !important;
        text-align: center !important;
    }
    
    .character-dashboard-content {
        grid-template-columns: 1fr !important;
    }
    
    .quick-actions-section {
        flex-direction: row !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
}

@media (max-width: 768px) {
    .progress-grid {
        grid-template-columns: 1fr !important;
    }
    
    .abilities-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Animations for progress bars */
.progress-bar > div {
    animation: fillProgress 1s ease-out;
}

@keyframes fillProgress {
    from { width: 0%; }
}

/* Hover effects */
.progress-card {
    transition: all 0.3s ease;
}

.progress-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(214, 166, 95, 0.2);
}
</style>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

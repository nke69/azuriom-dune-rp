@extends('layouts.app')

@section('title', 'Économie de l\'Épice - ' . trans('dune-rp::messages.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="economy-header dune-panel" style="text-align: center; padding: 50px 20px; background: linear-gradient(135deg, rgba(230,126,34,0.3), rgba(255,215,0,0.2));">
        <h1 class="dune-heading" style="font-size: 2.8rem; margin-bottom: 20px;">
            <i class="bi bi-lightning-charge"></i> Économie de l'Épice
        </h1>
        <p style="font-size: 1.2rem; color: var(--dune-sand); max-width: 800px; margin: 0 auto;">
            "Celui qui contrôle l'épice contrôle l'univers." Découvrez les flux économiques qui régissent l'Imperium.
        </p>
        
        {{-- Global Spice Indicator --}}
        <div class="global-spice-indicator" style="margin-top: 30px; display: inline-block; padding: 20px 40px; background: rgba(0,0,0,0.3); border-radius: 25px; border: 2px solid var(--dune-spice-glow);">
            <div style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Épice Totale en Circulation</div>
            <div class="spice-glow" style="font-size: 2.5rem; font-weight: bold;">
                {{ number_format($economyStats['total_spice'], 0) }} <span style="font-size: 1rem;">tonnes</span>
            </div>
        </div>
    </div>

    {{-- Economy Statistics --}}
    <div class="economy-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin: 40px 0;">
        <div class="stat-card dune-panel" style="text-align: center; padding: 25px; background: linear-gradient(135deg, rgba(30,74,140,0.1), rgba(0,0,0,0.2));">
            <div class="stat-icon" style="font-size: 3rem; color: var(--dune-blue-eyes); margin-bottom: 15px;">
                <i class="bi bi-building"></i>
            </div>
            <div class="stat-number spice-glow" style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">
                {{ $economyStats['houses_count'] }}
            </div>
            <div class="stat-label">Maisons Actives</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark); margin-top: 5px;">
                Participant à l'économie
            </div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 25px; background: linear-gradient(135deg, rgba(76,175,80,0.1), rgba(0,0,0,0.2));">
            <div class="stat-icon" style="font-size: 3rem; color: #4caf50; margin-bottom: 15px;">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="stat-number spice-glow" style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">
                {{ number_format($economyStats['daily_income'], 0) }}
            </div>
            <div class="stat-label">Revenus Quotidiens</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark); margin-top: 5px;">
                Tonnes d'épice gagnées aujourd'hui
            </div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 25px; background: linear-gradient(135deg, rgba(244,67,54,0.1), rgba(0,0,0,0.2));">
            <div class="stat-icon" style="font-size: 3rem; color: #f44336; margin-bottom: 15px;">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="stat-number spice-glow" style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">
                {{ number_format($economyStats['daily_expenses'], 0) }}
            </div>
            <div class="stat-label">Dépenses Quotidiennes</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark); margin-top: 5px;">
                Tonnes d'épice dépensées aujourd'hui
            </div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 25px; background: linear-gradient(135deg, rgba(255,152,0,0.1), rgba(0,0,0,0.2));">
            <div class="stat-icon" style="font-size: 3rem; color: #ff9800; margin-bottom: 15px;">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-number spice-glow" style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">
                {{ number_format($economyStats['average_reserves'], 0) }}
            </div>
            <div class="stat-label">Réserves Moyennes</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark); margin-top: 5px;">
                Par maison active
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="economy-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
        
        {{-- Top Houses by Spice --}}
        <div class="top-houses-section">
            <div class="section-header dune-panel" style="padding: 20px; margin-bottom: 20px; background: linear-gradient(135deg, rgba(230,126,34,0.2), rgba(0,0,0,0.1));">
                <h2 class="dune-heading" style="margin: 0; color: var(--dune-spice-glow); font-size: 1.8rem;">
                    <i class="bi bi-trophy"></i> Maisons les Plus Riches
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--dune-sand);">Classement par réserves d'épice</p>
            </div>
            
            @if($topHouses->count() > 0)
                <div class="houses-ranking">
                    @foreach($topHouses as $index => $house)
                        <div class="house-rank-item dune-panel fade-in" style="display: flex; align-items: center; padding: 20px; margin-bottom: 15px; position: relative; border-left: 4px solid {{ $house->color ?? 'var(--dune-sand)' }};" data-delay="{{ $index * 100 }}">
                            {{-- Rank Badge --}}
                            <div class="rank-badge" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(45deg, var(--dune-spice), var(--dune-spice-glow)); display: flex; align-items: center; justify-content: center; margin-right: 20px; position: relative;">
                                <span style="font-size: 1.2rem; font-weight: bold; color: white;">{{ $index + 1 }}</span>
                                @if($index == 0)
                                    <div class="crown" style="position: absolute; top: -8px; left: 50%; transform: translateX(-50%); color: #ffd700; font-size: 1.2rem;">
                                        <i class="bi bi-crown"></i>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- House Info --}}
                            <div class="house-info" style="flex: 1;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    @if($house->sigil_url)
                                        <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 12px; border: 2px solid {{ $house->color ?? 'var(--dune-sand)' }};">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 50%; margin-right: 12px; background: {{ $house->color ?? 'var(--dune-sand)' }}; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-shield" style="color: white; font-size: 1.2rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h3 style="margin: 0; color: {{ $house->color ?? 'var(--dune-spice-glow)' }}; font-size: 1.3rem;">
                                            {{ $house->name }}
                                        </h3>
                                        @if($house->leader)
                                            <p style="margin: 0; font-size: 0.9rem; color: var(--dune-sand);">
                                                Dirigé par {{ $house->leader->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- House Stats --}}
                                <div style="display: flex; gap: 20px; font-size: 0.9rem; color: var(--dune-sand);">
                                    <span>
                                        <i class="bi bi-people"></i> {{ $house->getActiveMembersCount() }} membres
                                    </span>
                                    <span>
                                        <i class="bi bi-gem"></i> {{ number_format($house->influence_points) }} influence
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Spice Amount --}}
                            <div class="spice-display" style="text-align: right; margin-left: 20px;">
                                <div class="spice-amount spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                                    {{ number_format($house->spice_reserves, 0) }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes d'épice</div>
                            </div>
                            
                            {{-- Action Button --}}
                            <div style="margin-left: 15px;">
                                <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="padding: 8px 12px; font-size: 0.9rem;">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div style="text-align: center; margin-top: 25px;">
                    <a href="{{ route('dune-rp.houses.index') }}" class="dune-button">
                        <i class="bi bi-list"></i> Voir Toutes les Maisons
                    </a>
                </div>
            @else
                <div class="empty-houses dune-panel" style="text-align: center; padding: 40px;">
                    <i class="bi bi-building-x" style="font-size: 3rem; color: var(--dune-sand-dark); margin-bottom: 15px;"></i>
                    <p style="color: var(--dune-sand);">Aucune maison active trouvée.</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="economy-sidebar">
            {{-- Market Overview --}}
            <div class="market-overview dune-panel" style="padding: 25px; margin-bottom: 25px; background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(0,0,0,0.1));">
                <h3 class="dune-heading" style="margin-bottom: 20px; color: var(--dune-spice-glow); font-size: 1.4rem;">
                    <i class="bi bi-graph-up-arrow"></i> Aperçu du Marché
                </h3>
                
                @if($economyStats['richest_house'])
                    <div class="market-leader" style="margin-bottom: 20px; padding: 15px; background: rgba(255,215,0,0.1); border-radius: 8px; border-left: 4px solid var(--dune-spice-glow);">
                        <div style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 5px;">Maison Dominante</div>
                        <div style="color: var(--dune-spice-glow); font-weight: bold; font-size: 1.1rem;">
                            {{ $economyStats['richest_house']->name }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                            {{ number_format($economyStats['richest_house']->spice_reserves, 0) }} tonnes
                        </div>
                    </div>
                @endif
                
                <div class="market-stats" style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);">Transactions Totales:</span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">
                            {{ number_format($economyStats['total_transactions']) }}
                        </span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);">Transactions Aujourd'hui:</span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">
                            {{ number_format($economyStats['daily_transactions']) }}
                        </span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: var(--dune-sand);">Flux Net Quotidien:</span>
                        <span style="color: {{ ($economyStats['daily_income'] - $economyStats['daily_expenses']) >= 0 ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                            {{ ($economyStats['daily_income'] - $economyStats['daily_expenses']) >= 0 ? '+' : '' }}{{ number_format($economyStats['daily_income'] - $economyStats['daily_expenses'], 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="recent-activity dune-panel" style="padding: 25px; margin-bottom: 25px;">
                <h3 class="dune-heading" style="margin-bottom: 20px; color: var(--dune-spice-glow); font-size: 1.4rem;">
                    <i class="bi bi-clock-history"></i> Activité Récente
                </h3>
                
                @if($recentTransactions->count() > 0)
                    <div class="transactions-feed" style="max-height: 400px; overflow-y: auto;">
                        @foreach($recentTransactions->take(10) as $transaction)
                            <div class="transaction-item" style="display: flex; align-items: center; padding: 12px; margin-bottom: 8px; background: rgba(30,74,140,0.05); border-radius: 6px; border-left: 3px solid {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--dune-spice)') }};">
                                <div class="transaction-icon" style="margin-right: 12px; color: {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--dune-spice)') }};">
                                    <i class="bi {{ $transaction->getTypeIcon() }}"></i>
                                </div>
                                
                                <div class="transaction-info" style="flex: 1;">
                                    <div style="font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 2px;">
                                        <strong>{{ $transaction->house->name }}</strong>
                                        <span style="color: var(--dune-sand-dark);">• {{ $transaction->getTypeName() }}</span>
                                    </div>
                                    @if($transaction->reason)
                                        <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                            {{ Str::limit($transaction->reason, 30) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="transaction-amount" style="text-align: right;">
                                    <div style="font-weight: bold; color: {{ $transaction->isPositive() ? '#4caf50' : '#f44336' }};">
                                        {{ $transaction->getFormattedAmount() }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: var(--dune-sand-dark);">
                                        {{ $transaction->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="#" class="dune-button secondary" style="font-size: 0.9rem; width: 100%;" onclick="alert('Fonctionnalité en développement')">
                            <i class="bi bi-list"></i> Voir Toutes les Transactions
                        </a>
                    </div>
                @else
                    <div style="text-align: center; padding: 30px; color: var(--dune-sand-dark);">
                        <i class="bi bi-hourglass" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p style="margin: 0;">Aucune transaction récente</p>
                    </div>
                @endif
            </div>

            {{-- Quick Tools --}}
            <div class="quick-tools dune-panel" style="padding: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--dune-spice-glow); font-size: 1.2rem;">
                    <i class="bi bi-tools"></i> Outils Économiques
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('dune-rp.economy.market') }}" class="dune-button secondary" style="width: 100%; text-align: center;" onclick="alert('Fonctionnalité en développement')">
                        <i class="bi bi-graph-up"></i> Analyse de Marché
                    </a>
                    
                    <a href="{{ route('dune-rp.economy.flow') }}" class="dune-button secondary" style="width: 100%; text-align: center;" onclick="alert('Fonctionnalité en développement')">
                        <i class="bi bi-arrow-left-right"></i> Flux d'Épice
                    </a>
                    
                    <a href="{{ route('dune-rp.houses.leaderboard') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-trophy"></i> Classements
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Economy Insights --}}
    <div class="economy-insights dune-panel" style="padding: 30px; background: linear-gradient(135deg, rgba(30,74,140,0.1), rgba(139,0,0,0.1));">
        <h2 class="dune-heading" style="text-align: center; margin-bottom: 30px; color: var(--dune-spice-glow); font-size: 2rem;">
            <i class="bi bi-lightbulb"></i> Analyses Économiques
        </h2>
        
        <div class="insights-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
            {{-- Market Concentration --}}
            <div class="insight-card" style="background: rgba(30,74,140,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid var(--dune-blue-eyes);">
                <h4 style="color: var(--dune-blue-eyes); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-pie-chart"></i> Concentration du Marché
                </h4>
                <div style="margin-bottom: 15px;">
                    @php
                        $topThreeSpice = $topHouses->take(3)->sum('spice_reserves');
                        $concentration = $economyStats['total_spice'] > 0 ? ($topThreeSpice / $economyStats['total_spice']) * 100 : 0;
                    @endphp
                    <div class="concentration-bar" style="background: rgba(44,24,16,0.5); height: 12px; border-radius: 6px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, var(--dune-blue-eyes), #4fc3f7); width: {{ $concentration }}%; transition: width 1s;"></div>
                    </div>
                </div>
                <p style="margin: 0; color: var(--dune-sand); font-size: 0.95rem; line-height: 1.5;">
                    Les 3 maisons les plus riches contrôlent <strong style="color: var(--dune-blue-eyes);">{{ number_format($concentration, 1) }}%</strong> 
                    de l'épice totale, montrant {{ $concentration > 60 ? 'une forte concentration' : ($concentration > 40 ? 'une concentration modérée' : 'une distribution équilibrée') }} du pouvoir économique.
                </p>
            </div>
            
            {{-- Economic Activity --}}
            <div class="insight-card" style="background: rgba(76,175,80,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid #4caf50;">
                <h4 style="color: #4caf50; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-activity"></i> Activité Économique
                </h4>
                <div style="margin-bottom: 15px;">
                    @php
                        $activityRate = $economyStats['houses_count'] > 0 ? ($economyStats['daily_transactions'] / $economyStats['houses_count']) : 0;
                    @endphp
                    <div class="activity-meter" style="display: flex; align-items: center; gap: 10px;">
                        <div style="font-size: 2rem; font-weight: bold; color: #4caf50;">{{ number_format($activityRate, 1) }}</div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.9rem; color: var(--dune-sand);">Transactions / Maison / Jour</div>
                            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                Niveau d'activité: {{ $activityRate > 2 ? 'Très élevé' : ($activityRate > 1 ? 'Élevé' : ($activityRate > 0.5 ? 'Modéré' : 'Faible')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <p style="margin: 0; color: var(--dune-sand); font-size: 0.95rem; line-height: 1.5;">
                    Le marché montre {{ $activityRate > 1 ? 'une forte dynamique' : 'une activité stable' }} avec en moyenne 
                    <strong style="color: #4caf50;">{{ number_format($activityRate, 1) }}</strong> transactions par maison quotidiennement.
                </p>
            </div>
            
            {{-- Economic Health --}}
            <div class="insight-card" style="background: rgba(255,152,0,0.1); padding: 25px; border-radius: 10px; border-left: 4px solid #ff9800;">
                <h4 style="color: #ff9800; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-heart-pulse"></i> Santé Économique
                </h4>
                <div style="margin-bottom: 15px;">
                    @php
                        $netFlow = $economyStats['daily_income'] - $economyStats['daily_expenses'];
                        $healthScore = $economyStats['total_spice'] > 0 ? min(100, max(0, (($netFlow + 1000) / 2000) * 100)) : 50;
                    @endphp
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="health-score" style="width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(#4caf50 0deg {{ $healthScore * 3.6 }}deg, rgba(44,24,16,0.3) {{ $healthScore * 3.6 }}deg 360deg); display: flex; align-items: center; justify-content: center; position: relative;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(15,15,35,0.9); display: flex; align-items: center; justify-content: center;">
                                <span style="font-weight: bold; color: #ff9800;">{{ number_format($healthScore, 0) }}%</span>
                            </div>
                        </div>
                        <div>
                            <div style="color: {{ $netFlow >= 0 ? '#4caf50' : '#f44336' }}; font-weight: bold; font-size: 1.1rem;">
                                {{ $netFlow >= 0 ? '+' : '' }}{{ number_format($netFlow, 0) }} tonnes/jour
                            </div>
                            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                Flux net quotidien
                            </div>
                        </div>
                    </div>
                </div>
                <p style="margin: 0; color: var(--dune-sand); font-size: 0.95rem; line-height: 1.5;">
                    L'économie présente {{ $healthScore > 70 ? 'une excellente santé' : ($healthScore > 50 ? 'une santé correcte' : 'des signes de tension') }} 
                    avec un équilibre {{ $netFlow >= 0 ? 'positif' : 'négatif' }} entre production et consommation.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* Animation delays for staggered appearance */
.house-rank-item:nth-child(1) { animation-delay: 0.1s; }
.house-rank-item:nth-child(2) { animation-delay: 0.2s; }
.house-rank-item:nth-child(3) { animation-delay: 0.3s; }
.house-rank-item:nth-child(4) { animation-delay: 0.4s; }
.house-rank-item:nth-child(5) { animation-delay: 0.5s; }

/* Crown animation for first place */
.crown {
    animation: crownGlow 2s ease-in-out infinite alternate;
}

@keyframes crownGlow {
    from { text-shadow: 0 0 5px #ffd700; }
    to { text-shadow: 0 0 15px #ffd700, 0 0 25px #ffd700; }
}

/* Scrollbar styling for transaction feed */
.transactions-feed::-webkit-scrollbar {
    width: 6px;
}

.transactions-feed::-webkit-scrollbar-track {
    background: rgba(214,166,95,0.1);
    border-radius: 3px;
}

.transactions-feed::-webkit-scrollbar-thumb {
    background: var(--dune-spice);
    border-radius: 3px;
}

/* Hover effects */
.house-rank-item {
    transition: all 0.3s ease;
}

.house-rank-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(214, 166, 95, 0.3);
}

.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(214, 166, 95, 0.2);
}

.insight-card {
    transition: all 0.3s ease;
}

.insight-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(214, 166, 95, 0.2);
}

/* Responsive design */
@media (max-width: 1024px) {
    .economy-content {
        grid-template-columns: 1fr !important;
    }
    
    .economy-stats {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
    }
}

@media (max-width: 768px) {
    .house-rank-item {
        flex-direction: column !important;
        text-align: center !important;
        gap: 15px !important;
    }
    
    .house-rank-item .house-info {
        order: 1;
    }
    
    .house-rank-item .spice-display {
        order: 2;
        margin: 0 !important;
    }
    
    .house-rank-item > div:last-child {
        order: 3;
        margin: 0 !important;
    }
    
    .insights-grid {
        grid-template-columns: 1fr !important;
    }
    
    .economy-header h1 {
        font-size: 2rem !important;
    }
}

/* Animation for progress bars */
.concentration-bar > div {
    animation: fillBar 2s ease-out;
}

@keyframes fillBar {
    from { width: 0%; }
}
</style>

<script>
// Animate counters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate spice numbers
    const spiceNumbers = document.querySelectorAll('.stat-number');
    spiceNumbers.forEach(number => {
        animateCounter(number);
    });
    
    // Animate health score
    const healthScore = document.querySelector('.health-score span');
    if (healthScore) {
        animateCounter(healthScore, '%');
    }
});

function animateCounter(element, suffix = '') {
    const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString() + suffix;
    }, 16);
}

// Auto-refresh data every 60 seconds
setInterval(() => {
    const refreshableElements = document.querySelectorAll('[data-auto-refresh]');
    if (refreshableElements.length > 0) {
        // Here you could implement AJAX refresh
        console.log('Auto-refresh triggered');
    }
}, 60000);

// Add real-time spice particle effects
function createSpiceParticle() {
    const particle = document.createElement('div');
    particle.style.cssText = `
        position: fixed;
        width: 3px;
        height: 3px;
        background: #FFD700;
        border-radius: 50%;
        pointer-events: none;
        z-index: 1000;
        left: ${Math.random() * 100}vw;
        top: 100vh;
        box-shadow: 0 0 6px #FFD700;
    `;
    
    document.body.appendChild(particle);
    
    particle.animate([
        { transform: 'translateY(0) scale(0)', opacity: 0 },
        { transform: 'translateY(-100vh) scale(1)', opacity: 1 },
        { transform: 'translateY(-200vh) scale(0)', opacity: 0 }
    ], {
        duration: 8000 + Math.random() * 4000,
        easing: 'linear'
    }).onfinish = () => {
        particle.remove();
    };
}

// Create particles occasionally
setInterval(createSpiceParticle, 2000 + Math.random() * 3000);
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

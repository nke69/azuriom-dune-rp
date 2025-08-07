@extends('layouts.app')

@section('title', 'Transactions - ' . $house->name)

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
    <style>
        .house-transactions-theme {
            --house-color: {{ $house->color ?? '#D6A65F' }};
        }
    </style>
@endpush

@section('content')
<div class="dune-container house-transactions-theme">
    {{-- Header Section --}}
    <div class="transactions-header dune-panel" style="padding: 40px 20px; background: linear-gradient(135deg, rgba(15,15,35,0.9), var(--house-color)40);">
        <div style="display: flex; align-items: center; justify-content: center; gap: 25px; max-width: 800px; margin: 0 auto;">
            @if($house->sigil_url)
                <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" style="width: 80px; height: 80px; border: 3px solid var(--house-color); border-radius: 50%;">
            @else
                <div style="width: 80px; height: 80px; border: 3px solid var(--house-color); border-radius: 50%; background: var(--house-color); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-shield" style="font-size: 2.5rem; color: white;"></i>
                </div>
            @endif
            
            <div style="text-align: center; flex: 1;">
                <h1 class="dune-heading" style="font-size: 2.5rem; margin-bottom: 10px; color: var(--house-color);">
                    {{ $house->name }}
                </h1>
                <p style="font-size: 1.2rem; color: var(--dune-sand); margin-bottom: 15px;">
                    <i class="bi bi-receipt"></i> Historique des Transactions d'Épice
                </p>
                @if($house->motto)
                    <p style="font-style: italic; color: var(--dune-sand); font-size: 1rem;">
                        "{{ $house->motto }}"
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- House Statistics Summary --}}
    <div class="house-stats-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px; border-left: 4px solid var(--house-color);">
            <div class="stat-icon" style="font-size: 2.5rem; color: var(--house-color); margin-bottom: 10px;">
                <i class="bi bi-safe"></i>
            </div>
            <div class="stat-number spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                {{ number_format($houseStats['current_reserves'], 0) }}
            </div>
            <div class="stat-label">Réserves Actuelles</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes d'épice</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px; border-left: 4px solid #4caf50;">
            <div class="stat-icon" style="font-size: 2.5rem; color: #4caf50; margin-bottom: 10px;">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="stat-number" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: #4caf50;">
                {{ number_format($houseStats['monthly_income'], 0) }}
            </div>
            <div class="stat-label">Revenus ce Mois</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes gagnées</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px; border-left: 4px solid #f44336;">
            <div class="stat-icon" style="font-size: 2.5rem; color: #f44336; margin-bottom: 10px;">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="stat-number" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: #f44336;">
                {{ number_format($houseStats['monthly_expenses'], 0) }}
            </div>
            <div class="stat-label">Dépenses ce Mois</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes dépensées</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center; padding: 20px; border-left: 4px solid {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '#4caf50' : '#f44336' }};">
            <div class="stat-icon" style="font-size: 2.5rem; color: {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '#4caf50' : '#f44336' }}; margin-bottom: 10px;">
                <i class="bi bi-graph-{{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? 'up' : 'down' }}"></i>
            </div>
            <div class="stat-number" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '#4caf50' : '#f44336' }};">
                {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '+' : '' }}{{ number_format($houseStats['monthly_income'] - $houseStats['monthly_expenses'], 0) }}
            </div>
            <div class="stat-label">Solde Mensuel</div>
            <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">bénéfice/perte</div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="filters-section dune-panel" style="padding: 25px; margin-bottom: 25px;">
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end;">
            <div class="filter-group">
                <label for="type" style="display: block; margin-bottom: 5px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-funnel"></i> Type de Transaction
                </label>
                <select id="type" name="type" class="dune-select">
                    <option value="">Tous les types</option>
                    @foreach(\Azuriom\Plugin\DuneRp\Models\SpiceTransaction::TYPES as $typeKey => $typeName)
                        <option value="{{ $typeKey }}" {{ request('type') == $typeKey ? 'selected' : '' }}>
                            {{ $typeName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_from" style="display: block; margin-bottom: 5px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-calendar"></i> Date de Début
                </label>
                <input type="date" id="date_from" name="date_from" class="dune-input" value="{{ request('date_from') }}">
            </div>
            
            <div class="filter-group">
                <label for="date_to" style="display: block; margin-bottom: 5px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-calendar-check"></i> Date de Fin
                </label>
                <input type="date" id="date_to" name="date_to" class="dune-input" value="{{ request('date_to') }}">
            </div>
            
            <div class="filter-group">
                <label for="min_amount" style="display: block; margin-bottom: 5px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-currency-exchange"></i> Montant Min.
                </label>
                <input type="number" id="min_amount" name="min_amount" class="dune-input" placeholder="0" min="0" value="{{ request('min_amount') }}">
            </div>
            
            <div class="filter-group">
                <label for="reason" style="display: block; margin-bottom: 5px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-search"></i> Raison
                </label>
                <input type="text" id="reason" name="reason" class="dune-input" placeholder="Rechercher..." value="{{ request('reason') }}">
            </div>
            
            <div class="filter-actions" style="display: flex; gap: 8px;">
                <button type="submit" class="dune-button" style="flex: 1;">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
                <a href="{{ route('dune-rp.economy.house-transactions', $house) }}" class="dune-button secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Transactions Content --}}
    <div class="transactions-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        {{-- Transactions List --}}
        <div class="transactions-main">
            @if($transactions->count() > 0)
                <div class="transactions-list">
                    @foreach($transactions as $transaction)
                        <div class="transaction-card dune-panel fade-in" style="padding: 20px; margin-bottom: 15px; border-left: 4px solid {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--house-color)') }};">
                            <div style="display: grid; grid-template-columns: auto 1fr auto auto; gap: 20px; align-items: center;">
                                {{-- Transaction Icon --}}
                                <div class="transaction-icon" style="width: 50px; height: 50px; border-radius: 50%; background: {{ $transaction->getTypeColor() == 'success' ? 'rgba(76,175,80,0.2)' : ($transaction->getTypeColor() == 'danger' ? 'rgba(244,67,54,0.2)' : 'rgba(214,166,95,0.2)') }}; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi {{ $transaction->getTypeIcon() }}" style="font-size: 1.5rem; color: {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--house-color)') }};"></i>
                                </div>
                                
                                {{-- Transaction Info --}}
                                <div class="transaction-details">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                        <h4 style="margin: 0; color: var(--dune-spice-glow); font-size: 1.1rem;">
                                            {{ $transaction->getTypeName() }}
                                        </h4>
                                        <span class="transaction-badge" style="padding: 2px 8px; background: {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--house-color)') }}; color: white; border-radius: 10px; font-size: 0.7rem; font-weight: bold;">
                                            {{ strtoupper($transaction->type) }}
                                        </span>
                                    </div>
                                    
                                    @if($transaction->reason)
                                        <p style="margin: 0 0 8px 0; color: var(--dune-sand); font-size: 0.95rem;">
                                            {{ $transaction->reason }}
                                        </p>
                                    @endif
                                    
                                    @if($transaction->relatedEvent)
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <i class="bi bi-calendar-event" style="color: var(--dune-blue-eyes);"></i>
                                            <a href="{{ route('dune-rp.events.show', $transaction->relatedEvent) }}" style="color: var(--dune-blue-eyes); text-decoration: none; font-size: 0.9rem;">
                                                {{ $transaction->relatedEvent->title }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    <div class="transaction-meta" style="display: flex; gap: 15px; font-size: 0.8rem; color: var(--dune-sand-dark);">
                                        <span>
                                            <i class="bi bi-clock"></i> {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span>
                                            <i class="bi bi-hash"></i> #{{ $transaction->id }}
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Transaction Amount --}}
                                <div class="transaction-amount" style="text-align: right;">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: {{ $transaction->isPositive() ? '#4caf50' : '#f44336' }}; margin-bottom: 5px;">
                                        {{ $transaction->type === 'expense' ? '-' : '+' }}{{ number_format($transaction->amount, 0) }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">
                                        tonnes d'épice
                                    </div>
                                </div>
                                
                                {{-- Transaction Actions --}}
                                <div class="transaction-actions">
                                    <div class="dropdown" style="position: relative;">
                                        <button class="dune-button secondary" style="padding: 6px 10px; font-size: 0.8rem;" onclick="toggleDropdown(this)">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: var(--dune-space); border: 2px solid var(--dune-sand); border-radius: 8px; min-width: 150px; z-index: 1000; margin-top: 5px;">
                                            <a href="#" class="dropdown-item" style="display: block; padding: 8px 12px; color: var(--dune-sand); text-decoration: none; font-size: 0.9rem; transition: background 0.3s;" onclick="showTransactionDetails({{ $transaction->id }})">
                                                <i class="bi bi-info-circle"></i> Détails
                                            </a>
                                            @if($transaction->relatedEvent)
                                                <a href="{{ route('dune-rp.events.show', $transaction->relatedEvent) }}" class="dropdown-item" style="display: block; padding: 8px 12px; color: var(--dune-sand); text-decoration: none; font-size: 0.9rem;">
                                                    <i class="bi bi-calendar-event"></i> Voir l'événement
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="pagination-wrapper" style="margin-top: 30px;">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="no-transactions dune-panel" style="text-align: center; padding: 60px;">
                    <i class="bi bi-receipt-cutoff" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--dune-sand); margin-bottom: 15px;">Aucune Transaction Trouvée</h3>
                    <p style="color: var(--dune-sand-dark); margin-bottom: 25px;">
                        {{ request()->hasAny(['type', 'date_from', 'date_to', 'min_amount', 'reason']) 
                            ? 'Aucune transaction ne correspond aux critères sélectionnés.' 
                            : 'Cette maison n\'a effectué aucune transaction pour le moment.' }}
                    </p>
                    
                    @if(request()->hasAny(['type', 'date_from', 'date_to', 'min_amount', 'reason']))
                        <a href="{{ route('dune-rp.economy.house-transactions', $house) }}" class="dune-button">
                            <i class="bi bi-arrow-clockwise"></i> Voir Toutes les Transactions
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="transactions-sidebar">
            {{-- Quick Stats --}}
            <div class="quick-stats-card dune-panel" style="padding: 20px; margin-bottom: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--house-color); font-size: 1.2rem;">
                    <i class="bi bi-speedometer2"></i> Résumé Rapide
                </h4>
                
                <div class="quick-stats-list" style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);">Total Revenus:</span>
                        <span style="color: #4caf50; font-weight: bold;">+{{ number_format($houseStats['total_income'], 0) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);">Total Dépenses:</span>
                        <span style="color: #f44336; font-weight: bold;">-{{ number_format($houseStats['total_expenses'], 0) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: var(--dune-sand);">Solde Net:</span>
                        <span style="color: {{ ($houseStats['total_income'] - $houseStats['total_expenses']) >= 0 ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                            {{ ($houseStats['total_income'] - $houseStats['total_expenses']) >= 0 ? '+' : '' }}{{ number_format($houseStats['total_income'] - $houseStats['total_expenses'], 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Transaction Types Chart --}}
            <div class="transaction-types-card dune-panel" style="padding: 20px; margin-bottom: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--house-color); font-size: 1.2rem;">
                    <i class="bi bi-pie-chart"></i> Types de Transactions
                </h4>
                
                @php
                    $typeStats = $transactions->groupBy('type')->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'total' => $group->sum('amount')
                        ];
                    });
                @endphp
                
                <div class="transaction-types-list">
                    @foreach(\Azuriom\Plugin\DuneRp\Models\SpiceTransaction::TYPES as $typeKey => $typeName)
                        @if(isset($typeStats[$typeKey]))
                            <div class="type-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; margin-bottom: 8px; background: rgba({{ $typeKey == 'income' ? '76,175,80' : ($typeKey == 'expense' ? '244,67,54' : '214,166,95') }}, 0.1); border-radius: 6px; border-left: 3px solid {{ $typeKey == 'income' ? '#4caf50' : ($typeKey == 'expense' ? '#f44336' : 'var(--house-color)') }};">
                                <div>
                                    <div style="color: var(--dune-spice-glow); font-weight: bold; font-size: 0.9rem;">{{ $typeName }}</div>
                                    <div style="color: var(--dune-sand-dark); font-size: 0.8rem;">{{ $typeStats[$typeKey]['count'] }} transactions</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: {{ $typeKey == 'income' ? '#4caf50' : ($typeKey == 'expense' ? '#f44336' : 'var(--house-color)') }}; font-weight: bold; font-size: 0.9rem;">
                                        {{ number_format($typeStats[$typeKey]['total'], 0) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="quick-actions-card dune-panel" style="padding: 20px;">
                <h4 style="margin: 0 0 15px 0; color: var(--house-color); font-size: 1.2rem;">
                    <i class="bi bi-lightning"></i> Actions Rapides
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-shield"></i> Voir la Maison
                    </a>
                    
                    <a href="{{ route('dune-rp.economy.index') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-graph-up"></i> Économie Générale
                    </a>
                    
                    <button onclick="exportTransactions()" class="dune-button secondary" style="width: 100%; text-align: center;">
                        <i class="bi bi-download"></i> Exporter CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Transaction Details Modal --}}
<div id="transactionModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
    <div class="modal-content dune-panel" style="max-width: 500px; width: 90%; padding: 30px;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="dune-heading" style="margin: 0; color: var(--house-color);">Détails de la Transaction</h3>
            <button onclick="closeModal()" class="dune-button secondary" style="padding: 6px 10px;">
                <i class="bi bi-x"></i>
            </button>
        </div>
        
        <div id="transactionDetails" class="modal-body">
            <!-- Transaction details will be loaded here -->
        </div>
    </div>
</div>

<style>
/* Dropdown styles */
.dropdown-item:hover {
    background: rgba(214,166,95,0.2);
}

/* Responsive design */
@media (max-width: 1024px) {
    .transactions-content {
        grid-template-columns: 1fr !important;
    }
    
    .transaction-card > div {
        grid-template-columns: auto 1fr auto !important;
        gap: 15px !important;
    }
    
    .transaction-actions {
        grid-column: span 3 !important;
        justify-self: end !important;
        margin-top: 10px !important;
    }
}

@media (max-width: 768px) {
    .filters-section form {
        grid-template-columns: 1fr !important;
    }
    
    .house-stats-summary {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
    }
    
    .transaction-card > div {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
        text-align: center !important;
    }
    
    .transaction-details {
        order: 1;
    }
    
    .transaction-amount {
        order: 2;
        text-align: center !important;
    }
    
    .transaction-actions {
        order: 3;
        justify-self: center !important;
        margin-top: 0 !important;
    }
}

/* Animation delays */
.transaction-card:nth-child(1) { animation-delay: 0.1s; }
.transaction-card:nth-child(2) { animation-delay: 0.2s; }
.transaction-card:nth-child(3) { animation-delay: 0.3s; }
.transaction-card:nth-child(4) { animation-delay: 0.4s; }
.transaction-card:nth-child(5) { animation-delay: 0.5s; }

/* Modal styles */
.modal.show {
    display: flex !important;
}

/* Hover effects */
.transaction-card {
    transition: all 0.3s ease;
}

.transaction-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(214, 166, 95, 0.2);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(214, 166, 95, 0.2);
}
</style>

<script>
function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isVisible = dropdown.style.display === 'block';
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Toggle current dropdown
    dropdown.style.display = isVisible ? 'none' : 'block';
}

function showTransactionDetails(transactionId) {
    // Close dropdown
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Find transaction data (in a real implementation, this would fetch from server)
    const modal = document.getElementById('transactionModal');
    const detailsContainer = document.getElementById('transactionDetails');
    
    // Mock transaction details - in real implementation, fetch from server
    detailsContainer.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="padding: 15px; background: rgba(214,166,95,0.1); border-radius: 8px;">
                <h5 style="margin: 0 0 10px 0; color: var(--dune-spice-glow);">Information Générale</h5>
                <p style="margin: 0; color: var(--dune-sand); font-size: 0.9rem;">ID: #${transactionId}</p>
            </div>
            <div style="text-align: center; color: var(--dune-sand-dark);">
                Chargement des détails...
            </div>
        </div>
    `;
    
    modal.classList.add('show');
}

function closeModal() {
    document.getElementById('transactionModal').classList.remove('show');
}

function exportTransactions() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    
    const exportUrl = `${window.location.pathname}/export?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Close modal when clicking outside
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Auto-submit filters on change
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#type');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});

// Animate statistics on load
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(number => {
        if (number.textContent && !isNaN(parseInt(number.textContent.replace(/[^\d]/g, '')))) {
            animateCounter(number);
        }
    });
});

function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/[^\d-]/g, ''));
    const isNegative = element.textContent.includes('-');
    const duration = 1500;
    const increment = Math.abs(target) / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= Math.abs(target)) {
            current = Math.abs(target);
            clearInterval(timer);
        }
        
        const displayValue = Math.floor(current);
        const prefix = isNegative ? '-' : (element.textContent.includes('+') ? '+' : '');
        element.textContent = prefix + displayValue.toLocaleString();
    }, 16);
}
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

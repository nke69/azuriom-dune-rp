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
    <div class="transactions-header dune-panel" style="padding: 40px 30px; background: linear-gradient(135deg, rgba(15,15,35,0.9), {{ $house->color }}40);">
        <div style="display: flex; align-items: center; gap: 25px; max-width: 1000px; margin: 0 auto;">
            {{-- House Sigil --}}
            <div style="width: 80px; height: 80px; border: 3px solid var(--house-color); border-radius: 50%; overflow: hidden;">
                @if($house->sigil_url)
                    <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; background: linear-gradient(45deg, var(--house-color), var(--dune-spice)); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-shield" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                @endif
            </div>
            
            {{-- Header Info --}}
            <div style="flex: 1;">
                <h1 class="dune-heading" style="margin: 0 0 10px 0; color: var(--house-color); font-size: 2.2rem;">
                    <i class="bi bi-receipt"></i> Transactions - {{ $house->name }}
                </h1>
                <p style="margin: 0; color: var(--dune-sand); font-size: 1.1rem;">
                    Historique complet des mouvements d'épice de la Maison
                </p>
            </div>
            
            {{-- Quick Stats --}}
            <div style="display: flex; gap: 20px;">
                <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 10px; border: 2px solid var(--house-color); min-width: 120px;">
                    <div class="spice-glow" style="font-size: 1.6rem; font-weight: bold; margin-bottom: 5px;">
                        {{ number_format($house->spice_reserves, 0) }}
                    </div>
                    <div style="font-size: 0.9rem; color: var(--dune-sand);">Réserves Actuelles</div>
                </div>
                
                <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 10px; border: 2px solid #4caf50; min-width: 120px;">
                    <div style="font-size: 1.6rem; font-weight: bold; margin-bottom: 5px; color: #4caf50;">
                        {{ number_format($houseStats['total_income'], 0) }}
                    </div>
                    <div style="font-size: 0.9rem; color: var(--dune-sand);">Revenus Totaux</div>
                </div>
                
                <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 10px; border: 2px solid #f44336; min-width: 120px;">
                    <div style="font-size: 1.6rem; font-weight: bold; margin-bottom: 5px; color: #f44336;">
                        {{ number_format($houseStats['total_expenses'], 0) }}
                    </div>
                    <div style="font-size: 0.9rem; color: var(--dune-sand);">Dépenses Totales</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Summary --}}
    <div class="filters-summary" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin: 30px 0;">
        {{-- Filters Section --}}
        <div class="filters-section dune-panel" style="padding: 25px;">
            <h2 style="margin: 0 0 20px 0; color: var(--house-color); font-size: 1.4rem;">
                <i class="bi bi-funnel"></i> Filtres de Recherche
            </h2>
            
            <form method="GET" class="filter-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
                {{-- Transaction Type --}}
                <div class="filter-group">
                    <label for="type" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-tags"></i> Type de Transaction
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
                
                {{-- Date From --}}
                <div class="filter-group">
                    <label for="date_from" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-calendar"></i> Date de Début
                    </label>
                    <input type="date" id="date_from" name="date_from" class="dune-input" 
                           value="{{ request('date_from') }}">
                </div>
                
                {{-- Date To --}}
                <div class="filter-group">
                    <label for="date_to" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-calendar"></i> Date de Fin
                    </label>
                    <input type="date" id="date_to" name="date_to" class="dune-input" 
                           value="{{ request('date_to') }}">
                </div>
                
                {{-- Min Amount --}}
                <div class="filter-group">
                    <label for="min_amount" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-currency-exchange"></i> Montant Min.
                    </label>
                    <input type="number" id="min_amount" name="min_amount" class="dune-input" 
                           value="{{ request('min_amount') }}" placeholder="0" min="0" step="0.01">
                </div>
                
                {{-- Max Amount --}}
                <div class="filter-group">
                    <label for="max_amount" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-currency-exchange"></i> Montant Max.
                    </label>
                    <input type="number" id="max_amount" name="max_amount" class="dune-input" 
                           value="{{ request('max_amount') }}" placeholder="∞" min="0" step="0.01">
                </div>
                
                {{-- Reason Search --}}
                <div class="filter-group">
                    <label for="reason" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-search"></i> Recherche
                    </label>
                    <input type="text" id="reason" name="reason" class="dune-input" 
                           value="{{ request('reason') }}" placeholder="Rechercher dans les raisons...">
                </div>
                
                {{-- Filter Actions --}}
                <div class="filter-actions" style="display: flex; gap: 10px;">
                    <button type="submit" class="dune-button" style="white-space: nowrap;">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                    <a href="{{ route('dune-rp.economy.house-transactions', $house) }}" class="dune-button secondary" style="white-space: nowrap;">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Summary Stats --}}
        <div class="summary-stats dune-panel" style="padding: 25px;">
            <h3 style="margin: 0 0 20px 0; color: var(--house-color); font-size: 1.4rem;">
                <i class="bi bi-graph-up"></i> Résumé de la Période
            </h3>
            
            <div class="stats-list" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(76,175,80,0.1); border-radius: 8px; border-left: 4px solid #4caf50;">
                    <span style="color: var(--dune-sand);">
                        <i class="bi bi-arrow-up-circle"></i> Revenus Mensuels
                    </span>
                    <span style="color: #4caf50; font-weight: bold;">
                        +{{ number_format($houseStats['monthly_income'], 0) }}
                    </span>
                </div>
                
                <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(244,67,54,0.1); border-radius: 8px; border-left: 4px solid #f44336;">
                    <span style="color: var(--dune-sand);">
                        <i class="bi bi-arrow-down-circle"></i> Dépenses Mensuelles
                    </span>
                    <span style="color: #f44336; font-weight: bold;">
                        -{{ number_format($houseStats['monthly_expenses'], 0) }}
                    </span>
                </div>
                
                <div class="stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(255,152,0,0.1); border-radius: 8px; border-left: 4px solid #ff9800;">
                    <span style="color: var(--dune-sand);">
                        <i class="bi bi-calculator"></i> Bilan Mensuel
                    </span>
                    <span style="color: {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                        {{ ($houseStats['monthly_income'] - $houseStats['monthly_expenses']) >= 0 ? '+' : '' }}{{ number_format($houseStats['monthly_income'] - $houseStats['monthly_expenses'], 0) }}
                    </span>
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                    <i class="bi bi-shield"></i> Retour à la Maison
                </a>
                
                <button onclick="exportTransactions()" class="dune-button secondary" style="width: 100%;">
                    <i class="bi bi-download"></i> Exporter CSV
                </button>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    @if($transactions->count() > 0)
        <div class="transactions-table-container dune-panel" style="padding: 0; overflow: hidden;">
            <div class="table-header" style="padding: 20px 25px; background: linear-gradient(135deg, var(--house-color)20, rgba(0,0,0,0.1)); border-bottom: 2px solid var(--house-color);">
                <h3 style="margin: 0; color: var(--house-color); font-size: 1.5rem;">
                    <i class="bi bi-list-ul"></i> Historique des Transactions
                    <span style="font-size: 1rem; color: var(--dune-sand); font-weight: normal;">
                        ({{ $transactions->total() }} {{ $transactions->total() > 1 ? 'transactions' : 'transaction' }})
                    </span>
                </h3>
            </div>
            
            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="dune-table" style="margin: 0; border: none;">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;">Date & Heure</th>
                            <th style="min-width: 120px;">Type</th>
                            <th style="min-width: 120px; text-align: right;">Montant</th>
                            <th style="min-width: 200px;">Raison</th>
                            <th style="min-width: 150px;">Événement Lié</th>
                            <th style="min-width: 120px; text-align: right;">Solde Après</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningBalance = $house->spice_reserves; @endphp
                        @foreach($transactions as $transaction)
                            @php 
                                // Calculate what the balance was before this transaction
                                if ($transaction->isPositive()) {
                                    $balanceAfter = $runningBalance;
                                    $runningBalance -= $transaction->amount;
                                } else {
                                    $balanceAfter = $runningBalance;
                                    $runningBalance += $transaction->amount;
                                }
                            @endphp
                            <tr style="border-left: 4px solid {{ $transaction->isPositive() ? '#4caf50' : '#f44336' }};">
                                <td style="font-family: monospace;">
                                    <div style="font-weight: bold; color: var(--dune-spice-glow);">
                                        {{ $transaction->created_at->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size: 0.9rem; color: var(--dune-sand-dark);">
                                        {{ $transaction->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="bi {{ $transaction->getTypeIcon() }}" style="color: {{ $transaction->getTypeColor() == 'success' ? '#4caf50' : ($transaction->getTypeColor() == 'danger' ? '#f44336' : 'var(--dune-spice)') }};"></i>
                                        <span class="transaction-type" style="padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; background: rgba({{ $transaction->isPositive() ? '76,175,80' : '244,67,54' }}, 0.2); color: {{ $transaction->isPositive() ? '#4caf50' : '#f44336' }};">
                                            {{ $transaction->getTypeName() }}
                                        </span>
                                    </div>
                                </td>
                                
                                <td style="text-align: right; font-family: monospace;">
                                    <div style="font-size: 1.1rem; font-weight: bold; color: {{ $transaction->isPositive() ? '#4caf50' : '#f44336' }};">
                                        {{ $transaction->isPositive() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes</div>
                                </td>
                                
                                <td>
                                    @if($transaction->reason)
                                        <div style="color: var(--dune-sand);">{{ $transaction->reason }}</div>
                                    @else
                                        <span style="color: var(--dune-sand-dark); font-style: italic;">Aucune raison spécifiée</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($transaction->relatedEvent)
                                        <a href="{{ route('dune-rp.events.show', $transaction->relatedEvent) }}" 
                                           style="color: var(--house-color); text-decoration: none; display: flex; align-items: center; gap: 5px;"
                                           onmouseover="this.style.textDecoration='underline'"
                                           onmouseout="this.style.textDecoration='none'">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ Str::limit($transaction->relatedEvent->title, 20) }}
                                        </a>
                                    @else
                                        <span style="color: var(--dune-sand-dark); font-style: italic;">Aucun événement</span>
                                    @endif
                                </td>
                                
                                <td style="text-align: right; font-family: monospace;">
                                    <div class="spice-glow" style="font-weight: bold; font-size: 1rem;">
                                        {{ number_format($balanceAfter, 2) }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--dune-sand-dark);">tonnes</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="empty-transactions dune-panel" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-receipt-cutoff" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 20px;"></i>
            <h3 style="color: var(--dune-sand); margin-bottom: 15px;">Aucune Transaction Trouvée</h3>
            <p style="color: var(--dune-sand); margin-bottom: 25px; max-width: 500px; margin-left: auto; margin-right: auto;">
                @if(request()->hasAny(['type', 'date_from', 'date_to', 'min_amount', 'max_amount', 'reason']))
                    Aucune transaction ne correspond à vos critères de recherche. Essayez de modifier les filtres.
                @else
                    Cette maison n'a pas encore d'historique de transactions d'épice.
                @endif
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                @if(request()->hasAny(['type', 'date_from', 'date_to', 'min_amount', 'max_amount', 'reason']))
                    <a href="{{ route('dune-rp.economy.house-transactions', $house) }}" class="dune-button secondary">
                        <i class="bi bi-arrow-clockwise"></i> Réinitialiser les Filtres
                    </a>
                @endif
                
                <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button">
                    <i class="bi bi-shield"></i> Retour à la Maison
                </a>
            </div>
        </div>
    @endif
</div>

<style>
/* Table improvements */
.dune-table tbody tr:hover {
    background: rgba({{ substr($house->color ?? 'D6A65F', 1) }}, 0.1);
}

.transaction-type {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive design */
@media (max-width: 1024px) {
    .filters-summary {
        grid-template-columns: 1fr !important;
    }
    
    .transactions-header > div {
        flex-direction: column !important;
        text-align: center !important;
        gap: 20px !important;
    }
    
    .transactions-header > div > div:last-child {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)) !important;
        gap: 10px !important;
        width: 100% !important;
    }
}

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr !important;
    }
    
    .transactions-header {
        padding: 30px 20px !important;
    }
    
    .dune-table {
        font-size: 0.9rem;
    }
    
    .dune-table th,
    .dune-table td {
        padding: 8px !important;
    }
}

/* Enhanced animations */
.stat-item {
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(214, 166, 95, 0.2);
}

/* Loading animations for dynamic content */
.transactions-table-container {
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Export functionality
function exportTransactions() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    
    const exportUrl = `${window.location.pathname}?${params.toString()}`;
    
    // Create temporary download link
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `transactions_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-submit form on certain filter changes
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitFields = ['type'];
    
    autoSubmitFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});

// Date range validation
document.getElementById('date_from')?.addEventListener('change', function() {
    const dateTo = document.getElementById('date_to');
    if (dateTo && this.value) {
        dateTo.min = this.value;
    }
});

document.getElementById('date_to')?.addEventListener('change', function() {
    const dateFrom = document.getElementById('date_from');
    if (dateFrom && this.value) {
        dateFrom.max = this.value;
    }
});

// Amount range validation
document.getElementById('min_amount')?.addEventListener('input', function() {
    const maxAmount = document.getElementById('max_amount');
    if (maxAmount && this.value) {
        maxAmount.min = this.value;
    }
});

document.getElementById('max_amount')?.addEventListener('input', function() {
    const minAmount = document.getElementById('min_amount');
    if (minAmount && this.value) {
        minAmount.max = this.value;
    }
});

// Enhanced search with debouncing
let searchTimeout;
document.getElementById('reason')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Could implement live search here
        console.log('Search for:', this.value);
    }, 500);
});

// Table sorting (could be enhanced with AJAX)
function sortTable(column) {
    console.log('Sort by:', column);
    // Implementation would go here
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+F to focus search
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('reason')?.focus();
    }
    
    // Ctrl+E to export
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportTransactions();
    }
});

// Add tooltips for transaction types
document.querySelectorAll('.transaction-type').forEach(element => {
    const tooltips = {
        'Revenus': 'Gain d\'épice par la maison',
        'Dépenses': 'Utilisation d\'épice par la maison',
        'Transfert': 'Échange d\'épice avec une autre maison',
        'Tribut': 'Épice reçue en tant que tribut',
        'Commerce': 'Transaction commerciale'
    };
    
    const tooltip = tooltips[element.textContent];
    if (tooltip) {
        element.title = tooltip;
    }
});
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

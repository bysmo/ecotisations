@extends('layouts.membre')

@section('title', 'Flux financiers')

@section('content')
<style>
    /* Stats Cards Styling */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
        color: white;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .stat-card .card-body {
        padding: 1.5rem;
        z-index: 2;
        position: relative;
    }
    .stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.15;
        z-index: 1;
        transform: rotate(-15deg);
    }
    .bg-gradient-cagnotte { background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); }
    .bg-gradient-tontine { background: linear-gradient(135deg, #2c7a7b 0%, #38b2ac 100%); }
    .bg-gradient-credit { background: linear-gradient(135deg, #744210 0%, #b7791f 100%); }
    .bg-gradient-solde { background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%); }

    /* Table Styling */
    .table-flux { margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
    .table-flux thead th {
        padding: 0.75rem 1rem !important;
        font-size: 0.8rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background-color: #f8fafc !important;
        color: #64748b !important;
        border-bottom: 2px solid #e2e8f0 !important;
        font-weight: 600 !important;
    }
    .table-flux tbody td {
        padding: 1rem !important;
        font-size: 0.85rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #334155 !important;
    }
    .table-flux tbody tr:hover { background-color: #f8fafc; }
    
    .badge-flux {
        padding: 0.35em 0.65em;
        font-weight: 500;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    .amount-positive { color: #10b981; font-weight: 600; }
    .amount-negative { color: #ef4444; font-weight: 600; }
    
    .type-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 12px;
        font-size: 0.9rem;
    }
    .x-small { font-size: 0.65rem; }
    .fw-500 { font-weight: 500; }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><i class="bi bi-arrow-left-right text-primary me-2"></i>Flux financiers</h1>
        <p class="text-muted small mb-0">Suivez l'ensemble de vos transactions et activités financières en un seul endroit.</p>
    </div>
</div>

<!-- Dashboard Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-solde h-100">
            <div class="card-body">
                <div class="small opacity-75 mb-1 text-uppercase fw-bold">Solde Global</div>
                <h3 class="mb-0" style="font-weight: 400;">{{ number_format($stats['solde_global'], 0, ',', ' ') }} <small style="font-size: 0.8rem;">XOF</small></h3>
                <i class="bi bi-wallet2 icon-bg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-cagnotte h-100">
            <div class="card-body">
                <div class="small opacity-75 mb-1 text-uppercase fw-bold">Cagnottes</div>
                <h3 class="mb-0" style="font-weight: 400;">{{ number_format($stats['total_cagnottes'], 0, ',', ' ') }} <small style="font-size: 0.8rem;">XOF</small></h3>
                <i class="bi bi-receipt-cutoff icon-bg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-tontine h-100">
            <div class="card-body">
                <div class="small opacity-75 mb-1 text-uppercase fw-bold">Tontines / Épargne</div>
                <h3 class="mb-0" style="font-weight: 400;">{{ number_format($stats['total_tontines'], 0, ',', ' ') }} <small style="font-size: 0.8rem;">XOF</small></h3>
                <i class="bi bi-piggy-bank icon-bg"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-gradient-credit h-100">
            <div class="card-body">
                <div class="small opacity-75 mb-1 text-uppercase fw-bold">Nano-Crédits (Remboursés)</div>
                <h3 class="mb-0" style="font-weight: 400;">{{ number_format($stats['total_credits'], 0, ',', ' ') }} <small style="font-size: 0.8rem;">XOF</small></h3>
                <i class="bi bi-credit-card-2-front icon-bg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Liste des mouvements -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold" style="font-size: 1rem;"><i class="bi bi-list-stars me-2"></i>Journal d'activité</h5>
        
        <div class="d-flex gap-2 align-items-center">
            @if($annees->isNotEmpty())
                <form action="{{ route('membre.paiements') }}" method="GET" class="d-flex gap-2">
                    <select name="annee" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 100px;">
                        <option value="">Années</option>
                        @foreach($annees as $a)
                            <option value="{{ $a }}" {{ request('annee') == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted small"></i>
                <input type="text" class="form-control form-control-sm ps-4 table-search-flux" placeholder="Rechercher une opération..." style="width: 250px;">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-flux table-hover" id="table-flux-financiers">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opération</th>
                        <th>Type</th>
                        <th>Compte</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $m)
                        @php
                            $isEntree = $m->sens === 'entree';
                            $typeLabel = match($m->type) {
                                'cotisation' => ['label' => 'Cagnotte', 'icon' => 'bi-receipt-cutoff', 'bg' => '#e0e7ff', 'text' => '#4338ca'],
                                'epargne', 'epargne_libre' => ['label' => 'Tontine', 'icon' => 'bi-piggy-bank', 'bg' => '#ccfbf1', 'text' => '#0f766e'],
                                'remboursement_credit' => ['label' => 'Remb. Crédit', 'icon' => 'bi-credit-card-2-front', 'bg' => '#fef3c7', 'text' => '#b45309'],
                                'deboursement_credit' => ['label' => 'Débours. Crédit', 'icon' => 'bi-cash-coin', 'bg' => '#dcfce7', 'text' => '#15803d'],
                                'commission_garantie' => ['label' => 'Gains Garant', 'icon' => 'bi-shield-check', 'bg' => '#fae8ff', 'text' => '#a21caf'],
                                'remboursement' => ['label' => 'Remb. Reçu', 'icon' => 'bi-arrow-counterclockwise', 'bg' => '#fee2e2', 'text' => '#b91c1c'],
                                default => ['label' => ucfirst($m->type), 'icon' => 'bi-dot', 'bg' => '#f1f5f9', 'text' => '#475569'],
                            };
                        @endphp
                        <tr class="{{ isset($m->source_type) && $m->source_type === 'attente' ? 'table-warning opacity-75' : '' }}">
                            <td>
                                <div class="fw-bold">{{ $m->date_operation->format('d/m/Y') }}</div>
                                <div class="text-muted x-small">{{ $m->date_operation->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="type-icon" style="background-color: {{ $typeLabel['bg'] }}; color: {{ $typeLabel['text'] }};">
                                        <i class="bi {{ $typeLabel['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $m->libelle }}
                                            @if(isset($m->source_type) && $m->source_type === 'attente')
                                                <span class="badge bg-warning text-dark ms-1 p-1 small shadow-sm" style="font-size: 0.55rem; animation: pulse 2s infinite;">
                                                    <i class="bi bi-hourglass-split"></i> EN ATTENTE
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">{{ \Illuminate\Support\Str::limit($m->notes, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-flux" style="background-color: {{ $typeLabel['bg'] }}; color: {{ $typeLabel['text'] }};">
                                    {{ $typeLabel['label'] }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-500 text-secondary">{{ $m->caisse->nom ?? ($m->mode_paiement ?? '-') }}</div>
                            </td>
                            <td class="text-end">
                                <span class="{{ $isEntree ? 'amount-positive' : 'amount-negative' }}">
                                    {{ $isEntree ? '+' : '-' }} {{ number_format($m->montant, 0, ',', ' ') }}
                                </span>
                                <div class="text-muted x-small text-uppercase">XOF</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <p class="text-muted mt-3 mb-0">Aucun flux financier enregistré pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mouvements->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $mouvements->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.querySelector('.table-search-flux').addEventListener('input', function() {
    var q = this.value.trim().toLowerCase();
    document.querySelectorAll('#table-flux-financiers tbody tr').forEach(function(tr) {
        var text = tr.textContent.replace(/\s+/g, ' ').toLowerCase();
        tr.style.display = text.indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>
@endsection

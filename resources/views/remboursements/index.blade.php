@extends('layouts.app')

@section('title', 'Gestion des Remboursements')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-arrow-counterclockwise"></i> Gestion des Remboursements</h1>
</div>

<!-- Statistiques -->
<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: var(--primary-dark-blue);">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Total</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['total'] }}</h5>
                    </div>
                    <i class="bi bi-list-ul" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #17a2b8;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">En attente</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['en_attente'] }}</h5>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #198754;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Approuvés</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['approuves'] }}</h5>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #dc3545;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Refusés</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['refuses'] }}</h5>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Liste des Remboursements
    </div>
    <div class="card-body">
        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('remboursements.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="approuve" {{ request('statut') === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                        <option value="refuse" {{ request('statut') === 'refuse' ? 'selected' : '' }}>Refusé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" 
                           name="date_debut" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_debut') }}"
                           placeholder="Date début">
                </div>
                <div class="col-md-2">
                    <input type="date" 
                           name="date_fin" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_fin') }}"
                           placeholder="Date fin">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
            @if(request('search') || request('statut') || request('date_debut') || request('date_fin'))
                <div class="mt-2">
                    <a href="{{ route('remboursements.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($remboursements->count() > 0)
            <style>
                .table-remboursements {
                    margin-bottom: 0;
                }
                .table-remboursements thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: white !important;
                    background-color: var(--primary-dark-blue) !important;
                }
                .table-remboursements tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-remboursements tbody tr:last-child td {
                    border-bottom: none !important;
                }
                .table-remboursements .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-remboursements .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-remboursements .btn-group-sm > .btn,
                .table-remboursements .btn-group > .btn {
                    border-radius: 0.2rem !important;
                }
                table.table.table-remboursements.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-remboursements.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-remboursements.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-remboursements.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-remboursements table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Membre</th>
                            <th>Paiement</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date demande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remboursements as $remboursement)
                            <tr>
                                <td>{{ $remboursement->numero ?? '-' }}</td>
                                <td>{{ $remboursement->membre->nom }} {{ $remboursement->membre->prenom }}</td>
                                <td>{{ $remboursement->paiement->numero ?? '-' }}</td>
                                <td>
                                    {{ number_format($remboursement->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    @if($remboursement->statut === 'en_attente')
                                        En attente
                                    @elseif($remboursement->statut === 'approuve')
                                        Approuvé
                                    @else
                                        Refusé
                                    @endif
                                </td>
                                <td>{{ $remboursement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('remboursements.show', $remboursement) }}" class="btn btn-outline-primary btn-sm" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($remboursements->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $remboursements->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0">Aucun remboursement trouvé</p>
            </div>
        @endif
    </div>
</div>
@endsection

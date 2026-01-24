@extends('layouts.app')

@section('title', 'Historique des Mouvements')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-clock-history"></i> Historique des Mouvements de Caisses</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Tous les Mouvements
    </div>
    <div class="card-body">
        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('caisses.historique') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher par caisse, motif..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" 
                           name="date_debut" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_debut') }}"
                           placeholder="Date début">
                </div>
                <div class="col-md-3">
                    <input type="date" 
                           name="date_fin" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_fin') }}"
                           placeholder="Date fin">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
            @if(request('search') || request('date_debut') || request('date_fin'))
                <div class="mt-2">
                    <a href="{{ route('caisses.historique') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer les filtres
                    </a>
                </div>
            @endif
        </form>
        
        @if($mouvementsPaginated->count() > 0)
            <style>
                .table-historique {
                    margin-bottom: 0;
                }
                .table-historique thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-historique tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-historique tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-historique.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-historique.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-historique.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-historique.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-historique .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-historique .btn i {
                    font-size: 0.65rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-historique table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Caisse(s)</th>
                            <th>Montant</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mouvementsPaginated as $mouvement)
                            <tr>
                                <td>{{ $mouvement['data']->id ?? '-' }}</td>
                                <td>
                                    {{ $mouvement['date'] ? $mouvement['date']->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    @if($mouvement['type'] === 'transfert')
                                        <i class="bi bi-arrow-left-right"></i> Transfert
                                    @else
                                        <i class="bi bi-plus-square"></i> Approvisionnement
                                    @endif
                                </td>
                                <td>
                                    @if($mouvement['type'] === 'transfert')
                                        <div>
                                            <small class="text-muted">De:</small> {{ $mouvement['data']->caisseSource->nom ?? '-' }}<br>
                                            <small class="text-muted">Vers:</small> {{ $mouvement['data']->caisseDestination->nom ?? '-' }}
                                        </div>
                                    @else
                                        {{ $mouvement['data']->caisse->nom ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($mouvement['data']->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ $mouvement['data']->motif ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($mouvementsPaginated->hasPages() || $mouvementsPaginated->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $mouvementsPaginated->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun mouvement enregistré</p>
            </div>
        @endif
    </div>
</div>
@endsection

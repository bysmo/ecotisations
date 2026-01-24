@extends('layouts.app')

@section('title', 'Approvisionnements')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-square"></i> Approvisionnements de Caisse</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Approvisionnements</span>
        <a href="{{ route('caisses.approvisionnement.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Nouvel Approvisionnement
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('caisses.approvisionnement') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher..." 
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
                    <a href="{{ route('caisses.approvisionnement') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($approvisionnements->count() > 0)
            <style>
                .table-approvisionnement {
                    margin-bottom: 0;
                }
                .table-approvisionnement thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-approvisionnement tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-approvisionnement .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-approvisionnement .btn i {
                    font-size: 0.65rem !important;
                }
                .table-approvisionnement tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-approvisionnement.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-approvisionnement.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-approvisionnement.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-approvisionnement.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-approvisionnement table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Caisse</th>
                            <th>Montant</th>
                            <th>Motif</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvisionnements as $approvisionnement)
                            <tr>
                                <td>{{ $approvisionnement->id }}</td>
                                <td>{{ $approvisionnement->created_at ? $approvisionnement->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $approvisionnement->caisse->nom ?? '-' }}</td>
                                <td>
                                    {{ number_format($approvisionnement->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td>{{ $approvisionnement->motif ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="#" class="btn btn-outline-primary" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($approvisionnements->hasPages() || $approvisionnements->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $approvisionnements->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun approvisionnement enregistré</p>
                <a href="{{ route('caisses.approvisionnement.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Créer le premier approvisionnement
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Sorties de Caisses')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-dash-square"></i> Sorties de Caisses</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Sorties</span>
        <a href="{{ route('caisses.sortie.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouvelle sortie
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche -->
        <form method="GET" action="{{ route('caisses.sortie') }}" class="mb-3">
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
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </div>
            @if(request('search') || request('date_debut') || request('date_fin'))
                <div class="mt-2">
                    <a href="{{ route('caisses.sortie') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($sorties->count() > 0)
            <style>
                .table-sortie {
                    margin-bottom: 0;
                }
                .table-sortie thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-sortie tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-sortie tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-sortie.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-sortie.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-sortie.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-sortie.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-sortie .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-sortie .btn i {
                    font-size: 0.65rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-sortie table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Caisse</th>
                            <th>Montant</th>
                            <th>Motif</th>
                            <th>Notes</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sorties as $sortie)
                            <tr>
                                <td>{{ $sortie->id }}</td>
                                <td>
                                    {{ $sortie->date_sortie ? $sortie->date_sortie->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    {{ $sortie->caisse->nom ?? '-' }}
                                    @if($sortie->caisse && $sortie->caisse->numero)
                                        <small class="text-muted">({{ $sortie->caisse->numero }})</small>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($sortie->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ $sortie->motif ?? '-' }}
                                </td>
                                <td>
                                    {{ $sortie->notes ? \Illuminate\Support\Str::limit($sortie->notes, 50) : '-' }}
                                </td>
                                <td>
                                    {{ $sortie->created_at ? $sortie->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($sorties->hasPages() || $sorties->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $sorties->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune sortie enregistrée</p>
            </div>
        @endif
    </div>
</div>
@endsection

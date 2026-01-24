@extends('layouts.app')

@section('title', 'Journal de Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-journal-text"></i> Journal - {{ $caisse->nom }}</h1>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-arrow-down-circle"></i> Entrées</div>
            <div class="card-body">
                <span class="badge bg-success">{{ number_format($totalEntrees, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-arrow-up-circle"></i> Sorties</div>
            <div class="card-body">
                <span class="badge bg-danger">{{ number_format($totalSorties, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-calculator"></i> Net (période)</div>
            <div class="card-body">
                <span class="badge {{ $net >= 0 ? 'bg-primary' : 'bg-warning' }}">{{ number_format($net, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Mouvements</span>
        <a href="{{ route('caisses.show', $caisse) }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('caisses.mouvements', $caisse) }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Rechercher..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            @if(request('search') || request('type') || request('date_debut') || request('date_fin'))
                <div class="mt-2">
                    <a href="{{ route('caisses.mouvements', $caisse) }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>

        @if($mouvements->count() > 0)
            <style>
                table.table-mouvements {
                    margin-bottom: 0;
                }
                table.table-mouvements thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                    height: auto !important;
                }
                table.table-mouvements tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                    height: auto !important;
                }
                table.table-mouvements tbody tr {
                    height: auto !important;
                }
                table.table-mouvements tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-mouvements.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-mouvements.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-mouvements.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-mouvements.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                table.table-mouvements .btn,
                .table-mouvements .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                    height: auto !important;
                }
                table.table-mouvements .btn i,
                .table-mouvements .btn i {
                    font-size: 0.65rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-mouvements table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Sens</th>
                            <th>Montant</th>
                            <th>Libellé</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mouvements as $mvt)
                            <tr>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $mvt->id }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    {{ $mvt->date_operation ? $mvt->date_operation->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $mvt->type }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    @if($mvt->sens === 'entree')
                                        Entrée
                                    @else
                                        Sortie
                                    @endif
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    {{ number_format($mvt->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $mvt->libelle ?? '-' }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $mvt->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($mouvements->hasPages() || $mouvements->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $mouvements->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">Aucun mouvement pour cette caisse</p>
            </div>
        @endif
    </div>
</div>
@endsection


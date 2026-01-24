@extends('layouts.app')

@section('title', 'Rapports par Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-cash-coin"></i> Rapports par Caisse</h1>
</div>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('rapports.caisse') }}" class="row g-2">
            <div class="col-md-4">
                <label for="date_debut" class="form-label" style="font-weight: 300; font-size: 0.75rem;">Date début</label>
                <input type="date" 
                       name="date_debut" 
                       id="date_debut"
                       class="form-control form-control-sm" 
                       value="{{ $dateDebut }}">
            </div>
            <div class="col-md-4">
                <label for="date_fin" class="form-label" style="font-weight: 300; font-size: 0.75rem;">Date fin</label>
                <input type="date" 
                       name="date_fin" 
                       id="date_fin"
                       class="form-control form-control-sm" 
                       value="{{ $dateFin }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filtrer
                </button>
                <a href="{{ route('rapports.caisse') }}" class="btn btn-secondary btn-sm ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Statistiques par Caisse
    </div>
    <div class="card-body">
        @if($statistiques->count() > 0)
            <style>
                .table-rapports-caisse thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-caisse tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-caisse tbody tr:last-child td {
                    border-bottom: none !important;
                }
                .table-rapports-caisse .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-rapports-caisse .btn i {
                    font-size: 0.65rem !important;
                }
                table.table.table-rapports-caisse.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-rapports-caisse.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-rapports-caisse.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-rapports-caisse.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-rapports-caisse table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Caisse</th>
                            <th>Numéro</th>
                            <th>Solde actuel</th>
                            <th>Entrées (période)</th>
                            <th>Sorties (période)</th>
                            <th>Net (période)</th>
                            <th>Nombre paiements</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statistiques as $stat)
                            <tr>
                                <td>
                                    <strong>{{ $stat['caisse']->nom }}</strong>
                                </td>
                                <td>
                                    {{ $stat['caisse']->numero ?? '-' }}
                                </td>
                                <td>
                                    {{ number_format($stat['solde_actuel'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ number_format($stat['entrees'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ number_format($stat['sorties'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ number_format($stat['net'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ $stat['nombre_paiements'] }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('caisses.mouvements', $stat['caisse']) }}" class="btn btn-outline-primary" title="Voir le journal">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune caisse active</p>
            </div>
        @endif
    </div>
</div>
@endsection

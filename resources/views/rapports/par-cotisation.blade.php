@extends('layouts.app')

@section('title', 'Rapports par Cotisation')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-earmark-text"></i> Rapports par Cotisation</h1>
</div>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('rapports.cotisation') }}" class="row g-2">
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
                <a href="{{ route('rapports.cotisation') }}" class="btn btn-secondary btn-sm ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Statistiques par Cotisation
    </div>
    <div class="card-body">
        @if($statistiques->count() > 0)
            <style>
                .table-rapports-cotisation thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-cotisation tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-cotisation tbody tr:last-child td {
                    border-bottom: none !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-hover table-rapports-cotisation table-striped">
                    <thead>
                        <tr>
                            <th>Cotisation</th>
                            <th>Caisse</th>
                            <th>Type montant</th>
                            <th>Montant fixe</th>
                            <th>Nombre paiements</th>
                            <th>Nombre membres</th>
                            <th>Montant total</th>
                            <th>Moyenne</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statistiques as $stat)
                            <tr>
                                <td>
                                    <strong>{{ $stat['cotisation']->nom }}</strong>
                                </td>
                                <td>
                                    {{ $stat['cotisation']->caisse->nom ?? '-' }}
                                </td>
                                <td>
                                    {{ ucfirst($stat['cotisation']->type_montant ?? 'fixe') }}
                                </td>
                                <td>
                                    @if($stat['cotisation']->montant)
                                        {{ number_format($stat['cotisation']->montant, 0, ',', ' ') }} XOF
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $stat['nombre_paiements'] }}
                                </td>
                                <td>
                                    {{ $stat['nombre_membres'] }}
                                </td>
                                <td>
                                    {{ number_format($stat['montant_total'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    {{ number_format($stat['moyenne'], 0, ',', ' ') }} XOF
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($statistiques->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $statistiques->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune cotisation active</p>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Rapports par Membre')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people"></i> Rapports par Membre</h1>
</div>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('rapports.membre') }}" class="row g-2">
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
                <a href="{{ route('rapports.membre') }}" class="btn btn-secondary btn-sm ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Statistiques par Membre
    </div>
    <div class="card-body">
        @if($statistiques->count() > 0)
            <style>
                .table-rapports-membre thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-membre tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-rapports-membre tbody tr:last-child td {
                    border-bottom: none !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-hover table-rapports-membre table-striped">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Numéro</th>
                            <th>Email</th>
                            <th>Nombre paiements</th>
                            <th>Montant total payé</th>
                            <th>Montant engagé</th>
                            <th>Payé sur engagements</th>
                            <th>Reste engagements</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statistiques as $stat)
                            <tr>
                                <td>
                                    <strong>{{ $stat['membre']->nom_complet }}</strong>
                                </td>
                                <td>
                                    {{ $stat['membre']->numero ?? '-' }}
                                </td>
                                <td>
                                    {{ $stat['membre']->email ?? '-' }}
                                </td>
                                <td>
                                    {{ $stat['nombre_paiements'] }}
                                </td>
                                <td>
                                    {{ number_format($stat['montant_total'], 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    @if($stat['montant_engage'] > 0)
                                        {{ number_format($stat['montant_engage'], 0, ',', ' ') }} XOF
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['montant_paye_engagements'] > 0)
                                        {{ number_format($stat['montant_paye_engagements'], 0, ',', ' ') }} XOF
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['reste_engagements'] > 0)
                                        {{ number_format($stat['reste_engagements'], 0, ',', ' ') }} XOF
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun membre actif</p>
            </div>
        @endif
    </div>
</div>
@endsection

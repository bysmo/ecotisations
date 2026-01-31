@extends('layouts.membre')

@section('title', 'Mes nano crédits')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-wallet2"></i> Mes nano crédits
    </h1>
    <a href="{{ route('membre.nano-credits') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-phone"></i> Types disponibles
    </a>
</div>

<div class="card">
    <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-list-ul"></i> Mes demandes et crédits
    </div>
    <div class="card-body">
        @if($nanoCredits->count() > 0)
            <style>
                .table-mes-nano thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: #fff !important;
                    background-color: var(--primary-dark-blue) !important;
                }
                .table-mes-nano tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                }
                .table-mes-nano .btn {
                    padding: 0 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.2 !important;
                    height: 22px !important;
                    font-weight: 300 !important;
                }
                .table-mes-nano .btn i { font-size: 0.65rem !important; }
                .table-mes-nano tbody tr:last-child td { border-bottom: none !important; }
                table.table.table-mes-nano.table-hover tbody tr { background-color: #fff !important; }
                table.table.table-mes-nano.table-hover tbody tr:nth-child(even) { background-color: #d4dde8 !important; }
                table.table.table-mes-nano.table-hover tbody tr:hover { background-color: #b8c7d9 !important; }
            </style>
            <div class="table-responsive">
                <table class="table table-mes-nano table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Montant</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nanoCredits as $nc)
                            <tr>
                                <td>{{ $nc->created_at->format('d/m/Y') }}</td>
                                <td>{{ $nc->nanoCreditType->nom ?? '—' }}</td>
                                <td class="text-end">{{ number_format($nc->montant, 0, ',', ' ') }} XOF</td>
                                <td>
                                    @if(in_array($nc->statut, ['demande_en_attente', 'en_etude']))
                                        <span class="badge bg-warning text-dark">{{ $nc->statut_label }}</span>
                                    @elseif(in_array($nc->statut, ['debourse', 'en_remboursement', 'success']))
                                        <span class="badge bg-success">{{ $nc->statut_label }}</span>
                                    @elseif(in_array($nc->statut, ['refuse', 'failed']))
                                        <span class="badge bg-danger">{{ $nc->statut_label }}</span>
                                    @elseif($nc->statut === 'rembourse')
                                        <span class="badge bg-info">{{ $nc->statut_label }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $nc->statut_label }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('membre.nano-credits.show', $nc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Détail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">{{ $nanoCredits->links() }}</div>
        @else
            <p class="text-muted mb-0">Vous n'avez aucune demande de nano crédit. <a href="{{ route('membre.nano-credits') }}">Voir les types disponibles</a>.</p>
        @endif
    </div>
</div>
@endsection

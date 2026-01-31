@extends('layouts.membre')

@section('title', 'Nano crédit #' . $nanoCredit->id)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-phone"></i> Nano crédit #{{ $nanoCredit->id }}
    </h1>
    <a href="{{ route('membre.nano-credits.mes') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Mes nano crédits</a>
</div>

<div class="row mb-3">
    <div class="col-md-4 mb-2">
        <div class="card text-white" style="background: var(--primary-dark-blue);">
            <div class="card-body py-2">
                <small class="text-white-50">Montant</small>
                <h5 class="mb-0">{{ number_format($nanoCredit->montant, 0, ',', ' ') }} XOF</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card text-white" style="background: var(--primary-blue);">
            <div class="card-body py-2">
                <small class="text-white-50">Statut</small>
                <h5 class="mb-0">{{ $nanoCredit->statut_label }}</h5>
            </div>
        </div>
    </div>
    @if($nanoCredit->date_fin_remboursement)
    <div class="col-md-4 mb-2">
        <div class="card text-white" style="background: #28a745;">
            <div class="card-body py-2">
                <small class="text-white-50">Fin remboursement</small>
                <h5 class="mb-0">{{ $nanoCredit->date_fin_remboursement->format('d/m/Y') }}</h5>
            </div>
        </div>
    </div>
    @endif
</div>

@if($nanoCredit->isDebourse() && $nanoCredit->echeances->count() > 0)
    <div class="card mb-3">
        <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
            <i class="bi bi-calendar-check"></i> Tableau d'amortissement
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date échéance</th>
                            <th class="text-end">Montant</th>
                            <th>Statut</th>
                            <th>Date paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nanoCredit->echeances as $e)
                            <tr>
                                <td>{{ $e->date_echeance->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($e->montant, 0, ',', ' ') }} XOF</td>
                                <td>
                                    @if($e->statut === 'payee')
                                        <span class="badge bg-success">Payée</span>
                                    @elseif($e->statut === 'en_retard')
                                        <span class="badge bg-danger">En retard</span>
                                    @else
                                        <span class="badge bg-secondary">À venir</span>
                                    @endif
                                </td>
                                <td>{{ $e->paye_le ? $e->paye_le->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if($nanoCredit->versements->count() > 0)
    <div class="card">
        <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
            <i class="bi bi-cash-coin"></i> Historique des remboursements
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Montant</th>
                            <th>Mode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nanoCredit->versements->sortByDesc('date_versement') as $v)
                            <tr>
                                <td>{{ $v->date_versement->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($v->montant, 0, ',', ' ') }} XOF</td>
                                <td>{{ $v->mode_paiement }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    @if($nanoCredit->isDebourse())
        <div class="card">
            <div class="card-body text-center text-muted small">
                Aucun remboursement enregistré pour le moment. Les remboursements sont enregistrés par l'administration ou via les canaux prévus.
            </div>
        </div>
    @endif
@endif
@endsection

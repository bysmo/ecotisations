@extends('layouts.app')

@section('title', 'Détail de la Transaction')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="bi bi-diagram-3"></i> Écritures Comptables</h1>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-info-circle"></i> Info. Référence
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" style="font-size: 0.8rem;">
                    <tr>
                        <th class="bg-light ps-3" style="width: 40%;">Type</th>
                        <td>{{ class_basename($mouvement->reference_type) }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">ID Réf</th>
                        <td>#{{ $mouvement->reference_id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Date Opération</th>
                        <td>{{ $mouvement->date_operation ? $mouvement->date_operation->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Libellé Principal</th>
                        <td>{{ $mouvement->libelle }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                <span class="small fw-bold"><i class="bi bi-journal-check"></i> Grand Livre - Écritures Balancées</span>
                @php
                    $sumDebit = $mouvements->where('sens', 'entree')->sum('montant');
                    $sumCredit = $mouvements->where('sens', 'sortie')->sum('montant');
                    $isBalanced = abs($sumDebit - $sumCredit) < 0.01;
                @endphp
                <span class="badge {{ $isBalanced ? 'bg-success' : 'bg-danger' }} small">
                    {{ $isBalanced ? 'ÉQUILIBRÉ' : 'DÉSÉQUILIBRE' }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Compte / Caisse</th>
                                <th>Membre</th>
                                <th class="text-end">Débit (Entrée)</th>
                                <th class="text-end pe-3">Crédit (Sortie)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mouvements as $m)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold">{{ $m->caisse->nom }}</div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">{{ $m->caisse->numero_core_banking ?? $m->caisse->numero }}</div>
                                    </td>
                                    <td>
                                        @if($m->caisse->membre)
                                            <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                                {{ $m->caisse->membre->nom_complet }}
                                            </span>
                                        @else
                                            <span class="text-muted small">SYSTÈME</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace">
                                        @if($m->sens === 'entree')
                                            <span class="text-success fw-bold">{{ number_format($m->montant, 0, ',', ' ') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 font-monospace">
                                        @if($m->sens === 'sortie')
                                            <span class="text-danger fw-bold">{{ number_format($m->montant, 0, ',', ' ') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold border-top-2">
                            <tr>
                                <td colspan="2" class="text-end ps-3 pt-2">TOTAUX</td>
                                <td class="text-end pt-2 font-monospace">{{ number_format($sumDebit, 0, ',', ' ') }}</td>
                                <td class="text-end pe-3 pt-2 font-monospace">{{ number_format($sumCredit, 0, ',', ' ') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @if(!$isBalanced)
                <div class="card-footer bg-danger-subtle text-danger py-1 small">
                    <i class="bi bi-exclamation-triangle-fill"></i> Attention : Un déséquilibre de {{ number_format(abs($sumDebit - $sumCredit), 0, ',', ' ') }} XOF a été détecté.
                </div>
            @endif
        </div>
        
        <div class="mt-3 text-muted small px-2">
            <i class="bi bi-info-circle"></i> Ces écritures sont atomiques et liées à l'identifiant technique <code>{{ $mouvement->reference_type }}:{{ $mouvement->reference_id }}</code>.
        </div>
    </div>
</div>

<style>
    .font-monospace { font-family: 'Courier New', Courier, monospace !important; }
    .table-mouvements td { vertical-align: middle; }
</style>
@endsection

@extends('layouts.membre')

@section('title', 'Détails de la Cotisation')

@section('content')

<div class="page-header" style="background-color: white; padding: 0.6rem 1rem; margin-bottom: 1rem; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif; font-size: 0.9rem; margin: 0;">
        <i class="bi bi-receipt-cutoff"></i> Détails de la Cotisation
    </h1>
</div>

<div class="card mb-3">
    <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-info-circle"></i> Informations de la Cotisation
    </div>
    <br>
    <div class="card-body p-0">
        <style>
            .table-info-cotisation { margin-bottom: 0; font-weight: 300; font-family: 'Ubuntu', sans-serif; font-size: 0.75rem; }
            .table-info-cotisation thead th { padding: 0.15rem 0.35rem !important; font-size: 0.7rem !important; line-height: 1.05 !important; vertical-align: middle !important; font-weight: 300 !important; color: #fff !important; background-color: var(--primary-dark-blue) !important; border: 1px solid #dee2e6 !important; white-space: nowrap; }
            .table-info-cotisation tbody td { padding: 0.15rem 0.35rem !important; font-size: 0.75rem !important; line-height: 1.05 !important; vertical-align: middle !important; border: 1px solid #dee2e6 !important; color: var(--primary-dark-blue) !important; }
            .table-info-cotisation tbody tr { background-color: #ffffff !important; }
            .table-info-cotisation tbody tr:nth-child(even) td { background-color: #f8f9fa !important; }
        </style>
        <div class="table-responsive">
            <table class="table table-info-cotisation mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Fréquence</th>
                        <th>Montant</th>
                        <th>Visibilité</th>
                        <th>Tag</th>
                        <th>Caisse</th>
                        <th>Statut</th>
                        <th>Total payé</th>
                        @if($cotisation->description)<th>Description</th>@endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $cotisation->nom }}</td>
                        <td>{{ $cotisation->numero ?? '-' }}</td>
                        <td>{{ ucfirst($cotisation->type ?? 'N/A') }}</td>
                        <td>{{ $cotisation->frequence ? ucfirst($cotisation->frequence) : '-' }}</td>
                        <td><strong>{{ $cotisation->montant ? number_format((float) $cotisation->montant, 0, ',', ' ') . ' XOF' : 'Libre' }}</strong></td>
                        <td>{{ ($cotisation->visibilite ?? 'publique') === 'publique' ? 'Publique' : 'Privée' }}</td>
                        <td>{{ $cotisation->tag ?? '-' }}</td>
                        <td>{{ $cotisation->caisse->nom ?? '-' }}</td>
                        <td>@if($cotisation->actif)<span style="color: #28a745;">Active</span>@else<span style="color: #dc3545;">Inactive</span>@endif</td>
                        <td><strong style="color: #28a745;">{{ number_format($totalPaye, 0, ',', ' ') }} XOF</strong></td>
                        @if($cotisation->description)<td>{{ Str::limit($cotisation->description, 50) }}</td>@endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(!$adhesion)
<div class="card mb-3">
    <div class="card-body text-center py-4">
        <p class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
            @if(($cotisation->visibilite ?? 'publique') === 'publique')
                Vous devez adhérer à cette cotisation pour pouvoir effectuer des paiements.
            @else
                Cette cotisation est privée. Demandez l'adhésion à l'administrateur.
            @endif
        </p>
        <form action="{{ route('membre.cotisations.adherer', $cotisation) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {{ ($cotisation->visibilite ?? 'publique') === 'publique' ? 'Adhérer' : 'Demander l\'adhésion' }}
            </button>
        </form>
    </div>
</div>
@elseif($adhesion->statut === 'en_attente')
<div class="card mb-3">
    <div class="card-body text-center py-4">
        <i class="bi bi-clock" style="font-size: 2rem; color: #ffc107;"></i>
        <p class="mb-0 mt-2" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Votre demande d'adhésion est en attente de validation par l'administrateur.</p>
    </div>
</div>
@endif

@if($canPay)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <span><i class="bi bi-list-ul"></i> Historique des Paiements</span>
        @if($cotisation->actif)
            @if($paydunyaEnabled)
                <button type="button" 
                        class="btn btn-primary btn-sm" 
                        onclick="initierPaiementPayDunya({{ $cotisation->id }}, '{{ $cotisation->nom }}', {{ $cotisation->montant }})"
                        style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                    <i class="bi bi-phone"></i> Payer via PayDunya
                </button>
            @else
                <button type="button" 
                        class="btn btn-primary btn-sm" 
                        disabled
                        title="PayDunya n'est pas activé"
                        style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                    <i class="bi bi-phone"></i> Payer ma cotisation
                </button>
            @endif
        @endif
    </div>
    <div class="card-body">
        @if($paiements->count() > 0)
            <style>
                .table-paiements {
                    margin-bottom: 0;
                }
                .table-paiements thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.7rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                    background-color: #e9ecef !important;
                    border-bottom: 2px solid #dee2e6 !important;
                }
                .table-paiements tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.75rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-paiements tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-paiements.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-paiements.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-paiements.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-paiements.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-hover table-paiements mb-0">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Date de paiement</th>
                            <th>Montant</th>
                            <th>Mode de paiement</th>
                            <th>Caisse</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $paiement)
                            <tr>
                                <td>{{ $paiement->numero ?? '-' }}</td>
                                <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                <td><strong>{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</strong></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement ?? 'N/A')) }}</td>
                                <td>{{ $paiement->caisse->nom ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #e9ecef !important;">
                            <td colspan="2" style="font-weight: 400; font-family: 'Ubuntu', sans-serif;"><strong>Total :</strong></td>
                            <td colspan="3" style="font-weight: 400; font-family: 'Ubuntu', sans-serif;">
                                <strong style="color: #28a745;">{{ number_format($totalPaye, 0, ',', ' ') }} XOF</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                    Aucun paiement effectué pour cette cotisation
                </p>
            </div>
        @endif
    </div>
</div>
@endif

<div class="d-flex justify-content-between align-items-center">
    <a href="{{ route('membre.cotisations') }}" class="btn btn-secondary" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
    @if($canPay && $cotisation->actif)
        @if($paymentMethods && $paymentMethods->count() > 0)
            <div class="d-flex gap-2 align-items-center">
                <span style="font-size: 0.75rem; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">Moyen de paiement :</span>
                <div class="btn-group" role="group">
                    @foreach($paymentMethods as $method)
                        @if($method->code === 'paydunya' && $paydunyaEnabled)
                            <button type="button" 
                                    class="btn btn-primary" 
                                    onclick="initierPaiementPayDunya({{ $cotisation->id }}, '{{ $cotisation->nom }}', {{ $cotisation->montant }})"
                                    style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                @if($method->icon)
                                    <i class="{{ $method->icon }}"></i>
                                @endif
                                {{ $method->name }}
                            </button>
                        @elseif($method->code === 'paypal')
                            <button type="button" 
                                    class="btn btn-primary" 
                                    disabled
                                    title="PayPal sera bientôt disponible"
                                    style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                @if($method->icon)
                                    <i class="{{ $method->icon }}"></i>
                                @endif
                                {{ $method->name }}
                            </button>
                        @elseif($method->code === 'stripe')
                            <button type="button" 
                                    class="btn btn-primary" 
                                    disabled
                                    title="Stripe sera bientôt disponible"
                                    style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                @if($method->icon)
                                    <i class="{{ $method->icon }}"></i>
                                @endif
                                {{ $method->name }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <button type="button" 
                    class="btn btn-primary" 
                    disabled
                    title="Aucun moyen de paiement activé. Veuillez contacter l'administration."
                    style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <i class="bi bi-credit-card"></i> Payer ma cotisation
            </button>
        @endif
    @endif
</div>

<!-- Modal de confirmation de paiement PayDunya -->
<div class="modal fade" id="paydunyaConfirmModal" tabindex="-1" aria-labelledby="paydunyaConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--primary-dark-blue); color: white;">
                <h5 class="modal-title" id="paydunyaConfirmModalLabel" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                    <i class="bi bi-phone"></i> Confirmation de paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <p id="paydunyaConfirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Annuler</button>
                <button type="button" class="btn btn-primary" id="paydunyaConfirmButton" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                    <i class="bi bi-check-circle"></i> Confirmer le paiement
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if($paydunyaEnabled)
<script>
let currentCotisationId = null;

function initierPaiementPayDunya(cotisationId, nomCotisation, montant) {
    currentCotisationId = cotisationId;
    
    // Mettre à jour le message du modal
    const message = 'Voulez-vous payer la cotisation "<strong>' + nomCotisation + '</strong>" d\'un montant de <strong>' + new Intl.NumberFormat('fr-FR').format(montant) + ' XOF</strong> ?';
    document.getElementById('paydunyaConfirmMessage').innerHTML = message;
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('paydunyaConfirmModal'));
    modal.show();
}

// Gérer le clic sur le bouton de confirmation
document.addEventListener('DOMContentLoaded', function() {
    const confirmButton = document.getElementById('paydunyaConfirmButton');
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            if (currentCotisationId) {
                // Créer un formulaire pour soumettre la requête POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("membre.cotisations.paydunya", ":id") }}'.replace(':id', currentCotisationId);
                
                // Ajouter le token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Soumettre le formulaire
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});

// Afficher une notification toast selon le statut du paiement
// Le script s'exécute après le script du layout qui définit showToast
@if(isset($paymentStatus))
    @if($paymentStatus === 'success')
        if (typeof showToast === 'function') {
            showToast('{{ $paymentMessage ?? "Paiement effectué avec succès !" }}', 'success');
        } else {
            console.error('showToast function not available');
        }
    @elseif($paymentStatus === 'cancelled')
        if (typeof showToast === 'function') {
            showToast('{{ $paymentMessage ?? "Paiement annulé. Vous pouvez réessayer à tout moment." }}', 'warning');
        }
    @elseif($paymentStatus === 'pending')
        if (typeof showToast === 'function') {
            showToast('{{ $paymentMessage ?? "Paiement en attente de confirmation." }}', 'info');
        }
    @elseif($paymentStatus === 'error')
        if (typeof showToast === 'function') {
            showToast('{{ $paymentMessage ?? "Erreur lors du paiement. Veuillez réessayer." }}', 'error');
        }
    @endif
@endif
</script>
@endif
@endpush
@endsection

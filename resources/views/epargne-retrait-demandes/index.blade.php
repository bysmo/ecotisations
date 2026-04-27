@extends('layouts.app')

@section('title', 'Demandes de retrait de tontines')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-wallet2"></i> Demandes de retrait de tontines</h1>
</div>

<p class="text-muted small mb-3">Demandes envoyées par les membres pour débloquer les fonds cumulés sur leurs tontines (épargne + rémunération).</p>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-list-ul"></i> Liste des demandes</span>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="statut" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="traite" {{ request('statut') === 'traite' ? 'selected' : '' }}>Traité</option>
                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($demandes->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Plan / Souscription</th>
                            <th>Montant</th>
                            <th>Mode souhaité</th>
                            <th>Date demande</th>
                            <th>Statut</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandes as $demande)
                            <tr>
                                <td>{{ $demande->membre->nom_complet ?? '-' }}<br><small class="text-muted">{{ $demande->membre->numero ?? '' }}</small></td>
                                <td>{{ $demande->souscription->plan->nom ?? '-' }}<br><small class="text-muted">ID: #{{ $demande->souscription_id }}</small></td>
                                <td class="fw-bold">{{ number_format($demande->montant_demande ?? 0, 0, ',', ' ') }} XOF</td>
                                <td>
                                    @if($demande->mode_retrait === 'pispi')
                                        <span class="badge bg-info text-dark"><i class="bi bi-lightning-charge"></i> Pi-SPI</span>
                                    @else
                                        <span class="badge bg-light text-dark">Virement Interne</span>
                                    @endif
                                </td>
                                <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($demande->statut === 'en_attente')
                                        <span class="badge bg-warning text-dark px-2 py-1">En attente</span>
                                    @elseif($demande->statut === 'traite')
                                        <span class="badge bg-success px-2 py-1">Traité</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">Rejeté</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($demande->statut === 'en_attente')
                                        @php $defaultAlias = $demande->membre->defaultWalletAlias(); @endphp
                                        <div class="d-flex justify-content-end gap-1">
                                            <form action="{{ route('epargne-retrait-demandes.approve', $demande) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="via_pispi" value="{{ $demande->mode_retrait === 'pispi' ? '1' : '0' }}">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approuver ce retrait ?');">
                                                    Accepter
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $demande->id }}">
                                                Rejeter
                                            </button>
                                        </div>

                                        <!-- Modal Rejet -->
                                        <div class="modal fade text-start" id="rejectModal{{ $demande->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('epargne-retrait-demandes.reject', $demande) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Rejeter la demande #{{ $demande->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Motif du rejet</label>
                                                                <textarea name="commentaire" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Aucun action</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $demandes->withQueryString()->links() }}
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2 mb-0">Aucune demande de retrait.</p>
            </div>
        @endif
    </div>
</div>
@endsection

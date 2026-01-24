@extends('layouts.membre')

@section('title', 'Mes Remboursements')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-arrow-counterclockwise"></i> Mes Demandes de Remboursement</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Liste de mes demandes de remboursement
    </div>
    <div class="card-body">
        @if($remboursements->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Paiement</th>
                            <th>Montant</th>
                            <th>Raison</th>
                            <th>Statut</th>
                            <th>Date demande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remboursements as $remboursement)
                            <tr>
                                <td>{{ $remboursement->numero ?? '-' }}</td>
                                <td>{{ $remboursement->paiement->numero ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-warning">
                                        {{ number_format($remboursement->montant, 0, ',', ' ') }} XOF
                                    </span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($remboursement->raison, 50) }}</td>
                                <td>
                                    @if($remboursement->statut === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($remboursement->statut === 'approuve')
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-danger">Refusé</span>
                                    @endif
                                </td>
                                <td>{{ $remboursement->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDetails{{ $remboursement->id }}"
                                            title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal détails -->
                            <div class="modal fade" id="modalDetails{{ $remboursement->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Détails du remboursement</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Numéro</dt>
                                                <dd class="col-sm-8">{{ $remboursement->numero }}</dd>
                                                
                                                <dt class="col-sm-4">Paiement</dt>
                                                <dd class="col-sm-8">{{ $remboursement->paiement->numero ?? '-' }}</dd>
                                                
                                                <dt class="col-sm-4">Montant</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge bg-warning">
                                                        {{ number_format($remboursement->montant, 0, ',', ' ') }} XOF
                                                    </span>
                                                </dd>
                                                
                                                <dt class="col-sm-4">Raison</dt>
                                                <dd class="col-sm-8">{{ $remboursement->raison }}</dd>
                                                
                                                <dt class="col-sm-4">Statut</dt>
                                                <dd class="col-sm-8">
                                                    @if($remboursement->statut === 'en_attente')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @elseif($remboursement->statut === 'approuve')
                                                        <span class="badge bg-success">Approuvé</span>
                                                    @else
                                                        <span class="badge bg-danger">Refusé</span>
                                                    @endif
                                                </dd>
                                                
                                                @if($remboursement->commentaire_admin)
                                                    <dt class="col-sm-4">Commentaire admin</dt>
                                                    <dd class="col-sm-8">{{ $remboursement->commentaire_admin }}</dd>
                                                @endif
                                                
                                                <dt class="col-sm-4">Date demande</dt>
                                                <dd class="col-sm-8">{{ $remboursement->created_at->format('d/m/Y à H:i') }}</dd>
                                                
                                                @if($remboursement->traite_le)
                                                    <dt class="col-sm-4">Traité le</dt>
                                                    <dd class="col-sm-8">{{ $remboursement->traite_le->format('d/m/Y à H:i') }}</dd>
                                                @endif
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($remboursements->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $remboursements->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">Aucune demande de remboursement</p>
            </div>
        @endif
    </div>
</div>
@endsection

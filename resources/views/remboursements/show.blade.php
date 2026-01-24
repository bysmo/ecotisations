@extends('layouts.app')

@section('title', 'Détails du Remboursement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-arrow-counterclockwise"></i> Remboursement {{ $remboursement->numero }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Numéro</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <strong>{{ $remboursement->numero ?? '-' }}</strong>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($remboursement->statut === 'en_attente')
                            <span class="badge bg-warning">En attente</span>
                        @elseif($remboursement->statut === 'approuve')
                            <span class="badge bg-success">Approuvé</span>
                        @else
                            <span class="badge bg-danger">Refusé</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Membre</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $remboursement->membre->nom }} {{ $remboursement->membre->prenom }} ({{ $remboursement->membre->numero ?? '-' }})
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Paiement concerné</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <a href="{{ route('paiements.show', $remboursement->paiement) }}">{{ $remboursement->paiement->numero ?? '-' }}</a>
                        - {{ number_format($remboursement->paiement->montant, 0, ',', ' ') }} XOF
                        ({{ $remboursement->paiement->date_paiement->format('d/m/Y') }})
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Montant à rembourser</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-warning">
                            {{ number_format($remboursement->montant, 0, ',', ' ') }} XOF
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Raison</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $remboursement->raison ?? '-' }}
                    </dd>
                    
                    @if($remboursement->commentaire_admin)
                        <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Commentaire admin</dt>
                        <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ $remboursement->commentaire_admin }}
                        </dd>
                    @endif
                    
                    @if($remboursement->traitePar)
                        <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Traité par</dt>
                        <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ $remboursement->traitePar->name }}
                            @if($remboursement->traite_le)
                                le {{ $remboursement->traite_le->format('d/m/Y à H:i') }}
                            @endif
                        </dd>
                    @endif
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Date de demande</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $remboursement->created_at->format('d/m/Y à H:i') }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if($remboursement->statut === 'en_attente')
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <i class="bi bi-exclamation-triangle"></i> Actions
                </div>
                <div class="card-body">
                    <form action="{{ route('remboursements.approve', $remboursement) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="commentaire_approve" class="form-label" style="font-size: 0.75rem;">Commentaire (optionnel)</label>
                            <textarea name="commentaire_admin" id="commentaire_approve" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-check-circle"></i> Approuver
                        </button>
                    </form>
                    
                    <form action="{{ route('remboursements.reject', $remboursement) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="commentaire_reject" class="form-label" style="font-size: 0.75rem;">Commentaire <span class="text-danger">*</span></label>
                            <textarea name="commentaire_admin" id="commentaire_reject" class="form-control form-control-sm @error('commentaire_admin') is-invalid @enderror" rows="2" required></textarea>
                            @error('commentaire_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-x-circle"></i> Refuser
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-tools"></i> Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('remboursements.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

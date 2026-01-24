@extends('layouts.app')

@section('title', 'Approvisionnement de Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-square"></i> Approvisionnement de Caisse</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Approvisionner une Caisse
    </div>
    <div class="card-body">
        <form action="{{ route('caisses.approvisionnement.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="caisse_id" class="form-label">
                    Caisse à approvisionner <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('caisse_id') is-invalid @enderror" 
                        id="caisse_id" 
                        name="caisse_id" 
                        required>
                    <option value="">Sélectionner une caisse</option>
                    @foreach($caisses as $caisse)
                        <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                            {{ $caisse->nom }} (Solde actuel: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} XOF)
                        </option>
                    @endforeach
                </select>
                @error('caisse_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="montant" class="form-label">
                        Montant à ajouter (XOF) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           class="form-control @error('montant') is-invalid @enderror" 
                           id="montant" 
                           name="montant" 
                           value="{{ old('montant') }}" 
                           min="1" 
                           step="1" 
                           required>
                    @error('montant')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="motif" class="form-label">Motif</label>
                    <input type="text" 
                           class="form-control @error('motif') is-invalid @enderror" 
                           id="motif" 
                           name="motif" 
                           value="{{ old('motif') }}" 
                           placeholder="Raison de l'approvisionnement">
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('caisses.approvisionnement') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Approvisionner
                </button>
            </div>
        </form>
    </div>
</div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos de l'Approvisionnement
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-plus-square"></i> Qu'est-ce qu'un approvisionnement ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un approvisionnement consiste à ajouter des fonds à une caisse existante. Cette opération crédite le solde de la caisse et est enregistrée dans le journal des mouvements.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Cas d'usage
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Dépôt initial :</strong> Alimenter une nouvelle caisse</li>
                    <li><strong>Recharge :</strong> Ajouter des fonds à une caisse existante</li>
                    <li><strong>Contribution externe :</strong> Enregistrer un apport de fonds</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Traçabilité
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Tous les approvisionnements sont enregistrés dans le journal de la caisse avec le motif et la date, permettant un suivi complet des mouvements.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Transfert Inter Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-arrow-left-right"></i> Transfert Inter Caisse</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Effectuer un Transfert
    </div>
    <div class="card-body">
        <form action="{{ route('caisses.transfert.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="caisse_source_id" class="form-label">
                        Caisse source <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('caisse_source_id') is-invalid @enderror" 
                            id="caisse_source_id" 
                            name="caisse_source_id" 
                            required>
                        <option value="">Sélectionner une caisse</option>
                        @foreach($caisses as $caisse)
                            <option value="{{ $caisse->id }}" {{ old('caisse_source_id') == $caisse->id ? 'selected' : '' }}>
                                {{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} XOF)
                            </option>
                        @endforeach
                    </select>
                    @error('caisse_source_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="caisse_destination_id" class="form-label">
                        Caisse destination <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('caisse_destination_id') is-invalid @enderror" 
                            id="caisse_destination_id" 
                            name="caisse_destination_id" 
                            required>
                        <option value="">Sélectionner une caisse</option>
                        @foreach($caisses as $caisse)
                            <option value="{{ $caisse->id }}" {{ old('caisse_destination_id') == $caisse->id ? 'selected' : '' }}>
                                {{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} XOF)
                            </option>
                        @endforeach
                    </select>
                    @error('caisse_destination_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="montant" class="form-label">
                        Montant (XOF) <span class="text-danger">*</span>
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
                           placeholder="Raison du transfert">
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('caisses.transfert') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Effectuer le transfert
                </button>
            </div>
        </form>
    </div>
</div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Transferts
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-arrow-left-right"></i> Qu'est-ce qu'un transfert ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un transfert inter-caisse permet de déplacer des fonds d'une caisse source vers une caisse destination. Cette opération débite automatiquement la caisse source et crédite la caisse destination.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Vérifications
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Le solde de la caisse source doit être suffisant</li>
                    <li>Les deux caisses doivent être différentes</li>
                    <li>Le motif est obligatoire pour la traçabilité</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Astuce
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Les transferts sont enregistrés dans le journal de chaque caisse concernée, permettant un suivi complet de tous les mouvements.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

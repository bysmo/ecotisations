@extends('layouts.app')

@section('title', 'Modifier une Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier la Caisse</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations de la Caisse
            </div>
            <div class="card-body">
        <form action="{{ route('caisses.update', $caisse) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nom" class="form-label">
                    Nom de la caisse <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('nom') is-invalid @enderror" 
                       id="nom" 
                       name="nom" 
                       value="{{ old('nom', $caisse->nom) }}" 
                       required>
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3">{{ old('description', $caisse->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="solde_initial" class="form-label">
                        Solde initial (XOF)
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="solde_initial" 
                           value="{{ number_format($caisse->solde_initial, 0, ',', ' ') }} XOF" 
                           readonly
                           style="background-color: #e9ecef; cursor: not-allowed;">
                    <small class="text-muted" style="font-size: 0.7rem;">Le solde de la caisse ne peut pas être modifié directement. Il est mis à jour automatiquement par les mouvements (paiements, approvisionnements, transferts, etc.).</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="statut" class="form-label">
                        Statut <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('statut') is-invalid @enderror" 
                            id="statut" 
                            name="statut" 
                            required
                            {{ $caisse->solde_initial != 0 && old('statut', $caisse->statut) === 'active' ? '' : '' }}>
                        <option value="active" {{ old('statut', $caisse->statut) === 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ old('statut', $caisse->statut) === 'inactive' ? 'selected' : '' }}
                                {{ $caisse->solde_initial != 0 ? 'disabled' : '' }}>
                            Inactive
                        </option>
                    </select>
                    @if($caisse->solde_initial != 0)
                        <small class="text-warning" style="font-size: 0.7rem;">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Cette caisse ne peut pas être désactivée car son solde est différent de 0.
                        </small>
                    @endif
                    @error('statut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('caisses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-cash-coin"></i> Modification de la caisse
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Cette page vous permet de modifier les informations d'une caisse. Notez que certaines restrictions s'appliquent pour garantir l'intégrité des données financières.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lock"></i> Solde de la caisse
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Le solde ne peut pas être modifié directement depuis cette page. Il est automatiquement mis à jour par les mouvements de caisse (paiements, approvisionnements, transferts, sorties, etc.).
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Désactivation d'une caisse
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Une caisse ne peut être désactivée que si son solde est égal à 0. Cette mesure de sécurité empêche la désactivation accidentelle d'une caisse contenant des fonds.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-toggle-on"></i> Statut actif
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Une caisse active peut recevoir et effectuer des mouvements. Une caisse inactive ne peut plus être utilisée pour les transactions.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

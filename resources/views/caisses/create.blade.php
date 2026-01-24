@extends('layouts.app')

@section('title', 'Créer une Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Créer une Nouvelle Caisse</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations de la Caisse
            </div>
            <div class="card-body">
                <form action="{{ route('caisses.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom de la caisse <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
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
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">
                            Statut <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('statut') is-invalid @enderror" 
                                id="statut" 
                                name="statut" 
                                required>
                            <option value="active" {{ old('statut') === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('statut') === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Champ solde_initial masqué, toujours à 0 lors de la création -->
                    <input type="hidden" name="solde_initial" value="0">
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('caisses.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Caisses
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-cash-coin"></i> Qu'est-ce qu'une caisse ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Une caisse représente un compte financier dans votre organisation. Elle permet de gérer les fonds provenant des cotisations, paiements et autres mouvements financiers. Chaque caisse possède un solde qui est mis à jour automatiquement lors des opérations.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Fonctionnalités
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Numéro unique :</strong> Généré automatiquement au format XXXX-XXXX</li>
                    <li><strong>Solde initial :</strong> Toujours 0 à la création, alimenté par les mouvements</li>
                    <li><strong>Statut :</strong> Active/Inactive pour contrôler l'utilisation</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Bonnes pratiques
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Créez une caisse séparée pour chaque type de fonds (cotisations mensuelles, cotisations exceptionnelles, etc.). Cela facilite le suivi et la gestion de vos finances.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

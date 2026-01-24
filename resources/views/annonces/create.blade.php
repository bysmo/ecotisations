@extends('layouts.app')

@section('title', 'Nouvelle Annonce')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-megaphone"></i> Nouvelle Annonce</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Créer une Annonce
            </div>
            <div class="card-body">
                <form action="{{ route('annonces.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">
                            Titre <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('titre') is-invalid @enderror" 
                               id="titre" 
                               name="titre" 
                               value="{{ old('titre') }}" 
                               required>
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenu" class="form-label">
                            Contenu <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('contenu') is-invalid @enderror" 
                                  id="contenu" 
                                  name="contenu" 
                                  rows="5" 
                                  required>{{ old('contenu') }}</textarea>
                        @error('contenu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="font-size: 0.7rem;">Le contenu de l'annonce qui sera affiché aux membres</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="info" {{ old('type', 'info') === 'info' ? 'selected' : '' }}>Info</option>
                                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Succès</option>
                                <option value="danger" {{ old('type') === 'danger' ? 'selected' : '' }}>Danger</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut" 
                                    required>
                                <option value="active" {{ old('statut', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('statut') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="date" 
                                   class="form-control @error('date_debut') is-invalid @enderror" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   value="{{ old('date_debut') }}">
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Laisser vide pour afficher immédiatement</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" 
                                   class="form-control @error('date_fin') is-invalid @enderror" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   value="{{ old('date_fin') }}">
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Laisser vide pour afficher indéfiniment</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="ordre" class="form-label">Ordre d'affichage</label>
                            <input type="number" 
                                   class="form-control @error('ordre') is-invalid @enderror" 
                                   id="ordre" 
                                   name="ordre" 
                                   value="{{ old('ordre', 0) }}" 
                                   min="0">
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Plus petit = affiché en premier</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="segment" class="form-label">Segment membre</label>
                        <select class="form-select @error('segment') is-invalid @enderror" 
                                id="segment" 
                                name="segment">
                            <option value="">-- Tous les membres --</option>
                            @foreach($segments as $segment)
                                <option value="{{ $segment }}" {{ old('segment') === $segment ? 'selected' : '' }}>{{ $segment }}</option>
                            @endforeach
                        </select>
                        @error('segment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="font-size: 0.7rem;">
                            Limite cette annonce à un segment spécifique de membres. Si vide, l'annonce est visible par tous les membres.
                        </small>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('annonces.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Créer l'annonce
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Annonces
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-megaphone"></i> Qu'est-ce qu'une annonce ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Une annonce est un message affiché sur le tableau de bord des membres. Elle permet de communiquer des informations importantes, des événements ou des rappels.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-palette"></i> Types d'annonces
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Info :</strong> Information générale (bleu)</li>
                    <li><strong>Avertissement :</strong> Attention requise (jaune)</li>
                    <li><strong>Succès :</strong> Confirmation positive (vert)</li>
                    <li><strong>Danger :</strong> Information urgente (rouge)</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-calendar-range"></i> Dates d'affichage
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Vous pouvez définir une date de début et de fin pour l'affichage. Si non renseignées, l'annonce est affichée immédiatement et indéfiniment (selon son statut).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

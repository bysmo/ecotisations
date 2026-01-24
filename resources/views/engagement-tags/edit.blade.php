@extends('layouts.app')

@section('title', 'Modifier un Tag d\'Engagement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier le Tag d'Engagement</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations du Tag
            </div>
            <div class="card-body">
        <form action="{{ route('engagement-tags.update', $tag) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nom" class="form-label">
                    Nom du tag <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('nom') is-invalid @enderror" 
                       id="nom" 
                       name="nom" 
                       value="{{ old('nom', $tag->nom) }}" 
                       required>
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">Le nom du tag doit être unique</small>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3">{{ old('description', $tag->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('engagement-tags.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle"></i> À propos des Tags d'Engagement
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-pencil-square"></i> Modification d'un tag
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Vous pouvez modifier le nom et la description d'un tag d'engagement. Notez que le nom doit rester unique parmi tous les tags d'engagement.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Utilisation
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Les tags permettent de catégoriser les engagements</li>
                    <li>Facilitent la recherche et le filtrage</li>
                    <li>Améliorent l'organisation des données</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-info-circle"></i> Note
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Si vous modifiez le nom d'un tag, tous les engagements utilisant ce tag seront automatiquement mis à jour.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

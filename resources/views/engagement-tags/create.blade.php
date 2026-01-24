@extends('layouts.app')

@section('title', 'Créer un Tag d\'Engagement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Créer un Nouveau Tag d'Engagement</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations du Tag
            </div>
            <div class="card-body">
        <form action="{{ route('engagement-tags.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="nom" class="form-label">
                    Nom du tag <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('nom') is-invalid @enderror" 
                       id="nom" 
                       name="nom" 
                       value="{{ old('nom') }}" 
                       required
                       placeholder="Ex: Premium, Standard, VIP...">
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
                          rows="3"
                          placeholder="Description optionnelle du tag...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('engagement-tags.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle"></i> À propos des Tags d'Engagement
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-tags"></i> Qu'est-ce qu'un tag d'engagement ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un tag d'engagement permet de catégoriser et d'organiser vos engagements selon différents critères (Premium, Standard, VIP, etc.). Les tags facilitent la recherche et le filtrage des engagements.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Utilisation
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Assigner des tags aux engagements lors de leur création ou modification</li>
                    <li>Filtrer et rechercher les engagements par tag</li>
                    <li>Organiser les engagements pour une meilleure gestion</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-info-circle"></i> Note
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Le nom du tag doit être unique. Une fois créé, vous pourrez l'utiliser pour catégoriser vos engagements.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

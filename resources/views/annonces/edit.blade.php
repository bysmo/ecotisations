@extends('layouts.app')

@section('title', 'Modifier l\'Annonce')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-megaphone"></i> Modifier l'Annonce</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Modifier l'Annonce
            </div>
            <div class="card-body">
                <form action="{{ route('annonces.update', $annonce) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">
                            Titre <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('titre') is-invalid @enderror" 
                               id="titre" 
                               name="titre" 
                               value="{{ old('titre', $annonce->titre) }}" 
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
                                  required>{{ old('contenu', $annonce->contenu) }}</textarea>
                        @error('contenu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                                <option value="info" {{ old('type', $annonce->type) === 'info' ? 'selected' : '' }}>Info</option>
                                <option value="warning" {{ old('type', $annonce->type) === 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="success" {{ old('type', $annonce->type) === 'success' ? 'selected' : '' }}>Succès</option>
                                <option value="danger" {{ old('type', $annonce->type) === 'danger' ? 'selected' : '' }}>Danger</option>
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
                                <option value="active" {{ old('statut', $annonce->statut) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('statut', $annonce->statut) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                   value="{{ old('date_debut', $annonce->date_debut ? $annonce->date_debut->format('Y-m-d') : '') }}">
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" 
                                   class="form-control @error('date_fin') is-invalid @enderror" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   value="{{ old('date_fin', $annonce->date_fin ? $annonce->date_fin->format('Y-m-d') : '') }}">
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="ordre" class="form-label">Ordre d'affichage</label>
                            <input type="number" 
                                   class="form-control @error('ordre') is-invalid @enderror" 
                                   id="ordre" 
                                   name="ordre" 
                                   value="{{ old('ordre', $annonce->ordre) }}" 
                                   min="0">
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="segment" class="form-label">Segment membre</label>
                        <select class="form-select @error('segment') is-invalid @enderror" 
                                id="segment" 
                                name="segment">
                            <option value="">-- Tous les membres --</option>
                            @foreach($segments as $segment)
                                <option value="{{ $segment }}" {{ old('segment', $annonce->segment) === $segment ? 'selected' : '' }}>{{ $segment }}</option>
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
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

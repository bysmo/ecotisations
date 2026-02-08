@extends('layouts.app')

@section('title', 'Modifier Template')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Modifier Template</h1>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Modifier le Template
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom', $emailTemplate->nom) }}" 
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="paiement" {{ old('type', $emailTemplate->type) === 'paiement' ? 'selected' : '' }}>Paiement</option>
                                <option value="engagement" {{ old('type', $emailTemplate->type) === 'engagement' ? 'selected' : '' }}>Engagement</option>
                                <option value="membre_inscrit" {{ old('type', $emailTemplate->type) === 'membre_inscrit' ? 'selected' : '' }}>Enregistrement du membre</option>
                                <option value="nano_credit_octroye" {{ old('type', $emailTemplate->type) === 'nano_credit_octroye' ? 'selected' : '' }}>Nano crédit octroyé</option>
                                <option value="autre" {{ old('type', $emailTemplate->type) === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sujet" class="form-label">
                            Sujet de l'email <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('sujet') is-invalid @enderror" 
                               id="sujet" 
                               name="sujet" 
                               value="{{ old('sujet', $emailTemplate->sujet) }}" 
                               required>
                        @error('sujet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="corps" class="form-label">
                            Corps de l'email <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('corps') is-invalid @enderror" 
                                  id="corps" 
                                  name="corps" 
                                  rows="10" 
                                  required>{{ old('corps', $emailTemplate->corps) }}</textarea>
                        @error('corps')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="actif" 
                                   name="actif" 
                                   value="1"
                                   {{ old('actif', $emailTemplate->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Activer ce template
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
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

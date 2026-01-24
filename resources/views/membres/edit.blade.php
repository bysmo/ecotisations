@extends('layouts.app')

@section('title', 'Modifier un Membre')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier le Membre</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Informations du Membre
    </div>
    <div class="card-body">
        <form action="{{ route('membres.update', $membre) }}" method="POST">
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
                           value="{{ old('nom', $membre->nom) }}" 
                           required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="prenom" class="form-label">
                        Prénom <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('prenom') is-invalid @enderror" 
                           id="prenom" 
                           name="prenom" 
                           value="{{ old('prenom', $membre->prenom) }}" 
                           required>
                    @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $membre->email) }}" 
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" 
                           class="form-control @error('telephone') is-invalid @enderror" 
                           id="telephone" 
                           name="telephone" 
                           value="{{ old('telephone', $membre->telephone) }}">
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control @error('adresse') is-invalid @enderror" 
                          id="adresse" 
                          name="adresse" 
                          rows="2">{{ old('adresse', $membre->adresse) }}</textarea>
                @error('adresse')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date_adhesion" class="form-label">
                        Date d'adhésion <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('date_adhesion') is-invalid @enderror" 
                           id="date_adhesion" 
                           name="date_adhesion" 
                           value="{{ old('date_adhesion', $membre->date_adhesion ? $membre->date_adhesion->format('Y-m-d') : '') }}" 
                           required>
                    @error('date_adhesion')
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
                        <option value="actif" {{ old('statut', $membre->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $membre->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspendu" {{ old('statut', $membre->statut) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                    @error('statut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="segment" class="form-label">Segment</label>
                <select class="form-select @error('segment') is-invalid @enderror" 
                        id="segment" 
                        name="segment">
                    <option value="">-- Aucun segment --</option>
                    @foreach($segments as $seg)
                        <option value="{{ $seg }}" {{ old('segment', $membre->segment) === $seg ? 'selected' : '' }}>{{ $seg }}</option>
                    @endforeach
                    <option value="__nouveau__" {{ old('segment') === '__nouveau__' ? 'selected' : '' }}>+ Ajouter un nouveau segment</option>
                </select>
                <div id="nouveauSegmentContainer" style="display: none; margin-top: 0.5rem;">
                    <input type="text" 
                           class="form-control form-control-sm" 
                           id="nouveauSegment" 
                           name="nouveau_segment" 
                           value="{{ old('nouveau_segment') }}"
                           placeholder="Nom du nouveau segment">
                    <small class="form-text text-muted" style="font-size: 0.65rem;">Saisissez le nom du nouveau segment</small>
                </div>
                @error('segment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('nouveau_segment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">Permet de segmenter les clients selon vos critères</small>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const segmentSelect = document.getElementById('segment');
                    const nouveauSegmentContainer = document.getElementById('nouveauSegmentContainer');
                    const nouveauSegmentInput = document.getElementById('nouveauSegment');
                    
                    segmentSelect.addEventListener('change', function() {
                        if (this.value === '__nouveau__') {
                            nouveauSegmentContainer.style.display = 'block';
                            nouveauSegmentInput.required = true;
                        } else {
                            nouveauSegmentContainer.style.display = 'none';
                            nouveauSegmentInput.required = false;
                            nouveauSegmentInput.value = '';
                        }
                    });
                    
                    // Vérifier si l'option "__nouveau__" est déjà sélectionnée au chargement
                    if (segmentSelect.value === '__nouveau__') {
                        nouveauSegmentContainer.style.display = 'block';
                        nouveauSegmentInput.required = true;
                    }
                });
            </script>
            
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">Laisser vide pour ne pas modifier</small>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('membres.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

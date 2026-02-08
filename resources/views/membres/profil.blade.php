@extends('layouts.membre')

@section('title', 'Mes Infos Personnelles')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-circle"></i> Mes Infos Personnelles</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square"></i> Modifier mes informations
            </div>
            <div class="card-body">
                <form action="{{ route('membre.profil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="numero" class="form-label">Numéro de membre</label>
                        <input type="text" 
                               class="form-control" 
                               id="numero" 
                               value="{{ $membre->numero }}" 
                               disabled>
                        <small class="text-muted">Le numéro de membre ne peut pas être modifié</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nom" 
                                   value="{{ $membre->nom }}" 
                                   disabled>
                            <small class="text-muted">Le nom ne peut pas être modifié</small>
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="prenom" 
                                   value="{{ $membre->prenom }}" 
                                   disabled>
                            <small class="text-muted">Le prénom ne peut pas être modifié</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
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
                    
                    <div class="mb-3">
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
                    
                    <div class="mb-3">
                        <label for="date_adhesion" class="form-label">Date d'adhésion</label>
                        <input type="text" 
                               class="form-control" 
                               id="date_adhesion" 
                               value="{{ $membre->date_adhesion->format('d/m/Y') }}" 
                               disabled>
                        <small class="text-muted">La date d'adhésion ne peut pas être modifiée</small>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3" style="font-weight: 300; color: var(--primary-dark-blue);">Changer mon mot de passe</h6>
                    <p class="text-muted" style="font-size: 0.8rem;">Laissez vide si vous ne souhaitez pas modifier votre mot de passe</p>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 6 caractères</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation">
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header" style="background-color: var(--primary-dark-blue); color: white; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <i class="bi bi-info-circle"></i> À propos
            </div>
            <div class="card-body" style="font-size: 0.75rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <p><strong>Modification des informations personnelles</strong></p>
                <p>Vous pouvez modifier votre email, téléphone et adresse. Le nom, prénom, numéro de membre et date d'adhésion ne peuvent pas être modifiés.</p>
                
                <p class="mt-3"><strong><i class="bi bi-key"></i> Mot de passe</strong></p>
                <p>Laissez les champs de mot de passe vides si vous ne souhaitez pas le modifier. Utilisez un mot de passe fort pour votre sécurité.</p>
            </div>
        </div>
    </div>
</div>
@endsection

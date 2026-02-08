@extends('layouts.app')

@section('title', 'Nouveau Template d\'Email')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Nouveau Template d'Email</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Créer un Template
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                Nom <span class="text-danger">*</span>
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
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="paiement" {{ old('type', 'paiement') === 'paiement' ? 'selected' : '' }}>Paiement</option>
                                <option value="engagement" {{ old('type') === 'engagement' ? 'selected' : '' }}>Engagement</option>
                                <option value="membre_inscrit" {{ old('type') === 'membre_inscrit' ? 'selected' : '' }}>Enregistrement du membre</option>
                                <option value="nano_credit_octroye" {{ old('type') === 'nano_credit_octroye' ? 'selected' : '' }}>Nano crédit octroyé</option>
                                <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
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
                               value="{{ old('sujet') }}" 
                               required>
                        @error('sujet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="font-size: 0.7rem;">
                            Variables disponibles: @{{nom}}, @{{prenom}}, @{{date_paiement}}, @{{montant}}, @{{cotisation}}, etc.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="corps" class="form-label">
                            Corps de l'email <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('corps') is-invalid @enderror" 
                                  id="corps" 
                                  name="corps" 
                                  rows="10" 
                                  required>@if(old('corps')){{ old('corps') }}@elseChers @{{nom}} @{{prenom}},

Merci de trouver le récapitulatif du paiement de votre cotisation effectué le @{{date_paiement}}.

Montant: @{{montant}} XOF
Cotisation: @{{cotisation}}

Cordialement.@endif</textarea>
                        @error('corps')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="font-size: 0.7rem;">
                            Utilisez @{{variable}} pour insérer des variables dynamiques
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="actif" 
                                   name="actif" 
                                   value="1"
                                   {{ old('actif', true) ? 'checked' : '' }}>
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
                            <i class="bi bi-check-circle"></i> Créer le template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Templates d'Email
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-file-text"></i> Qu'est-ce qu'un template ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un template d'email est un modèle de message personnalisable qui sera envoyé automatiquement aux membres lors de certaines actions (paiement, engagement, etc.).
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-code-square"></i> Variables disponibles
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>@{{nom}} :</strong> Nom du membre</li>
                    <li><strong>@{{prenom}} :</strong> Prénom du membre</li>
                    <li><strong>@{{email}} :</strong> Email du membre</li>
                    <li><strong>@{{lien_validation}} :</strong> Lien de validation email (obligatoire pour <em>Enregistrement du membre</em>)</li>
                    <li><strong>@{{app_nom}} :</strong> Nom de l'application (Enregistrement du membre)</li>
                    <li><strong>@{{date_paiement}} :</strong> Date du paiement (Paiement)</li>
                    <li><strong>@{{montant}} :</strong> Montant payé ou octroyé</li>
                    <li><strong>@{{cotisation}} :</strong> Nom de la cotisation (Paiement)</li>
                    <li><strong>@{{type_nano}} :</strong> Nom du type de nano crédit (Nano crédit octroyé)</li>
                    <li><strong>@{{date_octroi}} :</strong> Date d'octroi (Nano crédit octroyé)</li>
                </ul>
                <p style="font-size: 0.7rem; line-height: 1.4; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #888; margin-top: 0.5rem;">
                    <strong>Enregistrement du membre</strong> : utilisé à l'inscription. Pensez à inclure <strong>@{{lien_validation}}</strong> dans le corps pour que le membre puisse valider son email.<br>
                    <strong>Nano crédit octroyé</strong> : utilisé lorsqu'un nano crédit est accordé au membre.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Syntaxe
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Utilisez <strong>@{{variable}}</strong> (avec deux accolades) pour insérer des variables dynamiques. Les variables sont remplacées automatiquement lors de l'envoi de l'email.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

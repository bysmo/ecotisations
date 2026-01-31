@extends('layouts.app')

@section('title', 'Créer un Membre')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Créer un Nouveau Membre</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Informations du Membre
    </div>
    <div class="card-body">
        <form action="{{ route('membres.store') }}" method="POST">
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
                    <label for="prenom" class="form-label">
                        Prénom <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('prenom') is-invalid @enderror" 
                           id="prenom" 
                           name="prenom" 
                           value="{{ old('prenom') }}" 
                           required>
                    @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        Email (optionnel)
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone_input" 
                           class="form-control @error('telephone') is-invalid @enderror" 
                           data-initial="{{ old('telephone') }}"
                           required>
                    <input type="hidden" name="telephone" id="full_telephone">
                    @error('telephone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control @error('adresse') is-invalid @enderror" 
                          id="adresse" 
                          name="adresse" 
                          rows="2">{{ old('adresse') }}</textarea>
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
                           value="{{ old('date_adhesion', date('Y-m-d')) }}" 
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
                        <option value="actif" {{ old('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspendu" {{ old('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
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
                        <option value="{{ $seg }}" {{ old('segment') === $seg ? 'selected' : '' }}>{{ $seg }}</option>
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
                <label for="password" class="form-label">
                    Mot de passe <span class="text-danger">*</span>
                </label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">Minimum 6 caractères</small>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('membres.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle"></i> À propos des Membres
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-person"></i> Qu'est-ce qu'un membre ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un membre est une personne inscrite dans votre organisation qui peut effectuer des paiements de cotisations. Chaque membre reçoit un numéro unique et peut se connecter pour consulter ses paiements.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Informations requises
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Nom et prénom :</strong> Identité complète</li>
                    <li><strong>Email :</strong> Pour les notifications et connexion</li>
                    <li><strong>Date d'adhésion :</strong> Date d'inscription</li>
                    <li><strong>Mot de passe :</strong> Minimum 6 caractères</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Statuts
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    <strong>Actif :</strong> Membre pouvant effectuer des paiements<br>
                    <strong>Inactif :</strong> Membre temporairement désactivé<br>
                    <strong>Suspendu :</strong> Membre suspendu par décision administrative
                </p>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/css/intlTelInput.css">
<style>
    .iti { width: 100%; }
    .iti__flag-container { border-radius: 4px 0 0 4px; }
    .iti--separate-dial-code input { padding-left: 95px !important; }
    .iti--allow-dropdown input { padding-left: 95px !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector("#telephone");
        const fullPhoneInput = document.querySelector("#full_telephone");
        
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "bf",
            preferredCountries: ["bf", "sn", "ci", "ml", "tg", "bj"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/js/utils.js",
            separateDialCode: true,
        });

        // Gérer la valeur initiale pour éviter les duplications
        const initialNumber = phoneInput.getAttribute('data-initial');
        if (initialNumber) {
            iti.setNumber(initialNumber);
        }

        // Mettre à jour le champ caché avant la soumission
        const form = document.querySelector('form[action$="/membres"]');
        if (form) {
            form.addEventListener("submit", function() {
                fullPhoneInput.value = iti.getNumber();
            });
        }
    });
</script>
@endpush
@endsection

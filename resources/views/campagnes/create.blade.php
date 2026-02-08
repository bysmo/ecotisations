@extends('layouts.app')

@section('title', 'Nouvelle Campagne d\'Email')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope-paper"></i> Nouvelle Campagne d'Email</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-envelope"></i> Informations de la Campagne
            </div>
            <div class="card-body">
                <form action="{{ route('campagnes.store') }}" method="POST" id="campagneForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la campagne <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
                               required
                               placeholder="Ex: Rappel assemblée générale">
                    </div>

                    <div class="mb-3">
                        <label for="sujet" class="form-label">Sujet de l'email <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="sujet" 
                               name="sujet" 
                               value="{{ old('sujet') }}" 
                               required
                               placeholder="Ex: Assemblée générale - {{ date('d/m/Y') }}">
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" 
                                  id="message" 
                                  name="message" 
                                  rows="10" 
                                  required>{{ old('message') }}</textarea>
                        <small class="text-muted">
                            Variables disponibles : @{{nom}}, @{{prenom}}, @{{nom_complet}}, @{{email}}, @{{telephone}}, @{{adresse}}, @{{date_adhesion}}
                        </small>
                    </div>

                    <hr>

                    <h6 class="mb-3">Ciblage des membres</h6>

                    <div class="mb-3">
                        <label class="form-label">Statut des membres</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="statut_membre[]" value="actif" id="statut_actif" {{ in_array('actif', old('statut_membre', ['actif'])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="statut_actif">Actif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="statut_membre[]" value="inactif" id="statut_inactif" {{ in_array('inactif', old('statut_membre', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="statut_inactif">Inactif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="statut_membre[]" value="suspendu" id="statut_suspendu" {{ in_array('suspendu', old('statut_membre', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="statut_suspendu">Suspendu</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cotisation_id" class="form-label">Cotisation (optionnel)</label>
                        <select class="form-select form-select-sm" id="cotisation_id" name="cotisation_id">
                            <option value="">Toutes les cotisations</option>
                            @foreach($cotisations as $cotisation)
                                <option value="{{ $cotisation->id }}" {{ old('cotisation_id') == $cotisation->id ? 'selected' : '' }}>
                                    {{ $cotisation->nom }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Limiter aux membres ayant effectué un paiement pour cette cotisation</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_adhesion_debut" class="form-label">Date d'adhésion - Début (optionnel)</label>
                            <input type="date" 
                                   class="form-control form-control-sm" 
                                   id="date_adhesion_debut" 
                                   name="date_adhesion_debut" 
                                   value="{{ old('date_adhesion_debut') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_adhesion_fin" class="form-label">Date d'adhésion - Fin (optionnel)</label>
                            <input type="date" 
                                   class="form-control form-control-sm" 
                                   id="date_adhesion_fin" 
                                   name="date_adhesion_fin" 
                                   value="{{ old('date_adhesion_fin') }}">
                        </div>
                    </div>

                    <div class="alert alert-info" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Prévisualisation :</strong> <span id="preview-count">-</span> membre(s) recevront cet email
                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="btn-preview">
                            <i class="bi bi-eye"></i> Actualiser
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('campagnes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send"></i> Envoyer la campagne
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Aide
            </div>
            <div class="card-body" style="font-size: 0.75rem;">
                <h6>Variables disponibles :</h6>
                <ul>
                    <li><code>@{{nom}}</code> - Nom du membre</li>
                    <li><code>@{{prenom}}</code> - Prénom du membre</li>
                    <li><code>@{{nom_complet}}</code> - Prénom + Nom</li>
                    <li><code>@{{email}}</code> - Email du membre</li>
                    <li><code>@{{telephone}}</code> - Téléphone</li>
                    <li><code>@{{adresse}}</code> - Adresse</li>
                    <li><code>@{{date_adhesion}}</code> - Date d'adhésion</li>
                </ul>
                <hr>
                <p class="mb-0"><strong>Note :</strong> Seuls les membres ayant une adresse email valide recevront le message.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnPreview = document.getElementById('btn-preview');
    const previewCount = document.getElementById('preview-count');
    
    function updatePreview() {
        const formData = new FormData(document.getElementById('campagneForm'));
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("campagnes.preview") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            previewCount.textContent = data.count || 0;
        })
        .catch(error => {
            console.error('Erreur:', error);
            previewCount.textContent = '-';
        });
    }
    
    if (btnPreview) {
        btnPreview.addEventListener('click', updatePreview);
    }
    
    // Mettre à jour la prévisualisation lors du changement des filtres
    ['statut_actif', 'statut_inactif', 'statut_suspendu', 'cotisation_id', 'date_adhesion_debut', 'date_adhesion_fin'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updatePreview);
        }
    });
    
    // Prévisualisation initiale
    updatePreview();
});
</script>
@endsection

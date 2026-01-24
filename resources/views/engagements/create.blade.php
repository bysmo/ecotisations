@extends('layouts.app')

@section('title', 'Créer un Engagement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Créer un Nouvel Engagement</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Informations de l'Engagement
    </div>
    <div class="card-body">
        <form action="{{ route('engagements.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="membre_id" class="form-label">
                        Membre <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('membre_id') is-invalid @enderror" 
                            id="membre_id" 
                            name="membre_id" 
                            required>
                        <option value="">Sélectionner un membre</option>
                        @foreach($membres as $membre)
                            <option value="{{ $membre->id }}" {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                {{ $membre->nom_complet }} ({{ $membre->numero }})
                            </option>
                        @endforeach
                    </select>
                    @error('membre_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="cotisation_id" class="form-label">
                        Cotisation <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('cotisation_id') is-invalid @enderror" 
                            id="cotisation_id" 
                            name="cotisation_id" 
                            required>
                        <option value="">Sélectionner une cotisation</option>
                        @foreach($cotisations as $cotisation)
                            <option value="{{ $cotisation->id }}" 
                                    data-montant="{{ $cotisation->montant }}"
                                    data-caisse="{{ $cotisation->caisse->nom ?? '-' }}"
                                    {{ old('cotisation_id') == $cotisation->id ? 'selected' : '' }}>
                                {{ $cotisation->nom }} 
                                @if($cotisation->montant)
                                    - {{ number_format($cotisation->montant, 0, ',', ' ') }} XOF
                                @else
                                    (Montant libre)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('cotisation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="caisse_info" class="form-label">
                        Caisse associée
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="caisse_info" 
                           readonly
                           style="background-color: #f8f9fa; cursor: not-allowed;">
                    <small class="form-text text-muted" style="font-size: 0.7rem;">Caisse liée à la cotisation sélectionnée</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="periodicite" class="form-label">
                        Périodicité <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('periodicite') is-invalid @enderror" 
                            id="periodicite" 
                            name="periodicite" 
                            required>
                        <option value="mensuelle" {{ old('periodicite', 'mensuelle') === 'mensuelle' ? 'selected' : '' }}>Mensuelle</option>
                        <option value="trimestrielle" {{ old('periodicite') === 'trimestrielle' ? 'selected' : '' }}>Trimestrielle</option>
                        <option value="semestrielle" {{ old('periodicite') === 'semestrielle' ? 'selected' : '' }}>Semestrielle</option>
                        <option value="annuelle" {{ old('periodicite') === 'annuelle' ? 'selected' : '' }}>Annuelle</option>
                        <option value="unique" {{ old('periodicite') === 'unique' ? 'selected' : '' }}>Unique</option>
                    </select>
                    @error('periodicite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="montant_engage" class="form-label">
                        Montant engagé (XOF) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           class="form-control @error('montant_engage') is-invalid @enderror" 
                           id="montant_engage" 
                           name="montant_engage" 
                           value="{{ old('montant_engage') }}" 
                           min="1" 
                           step="1" 
                           required>
                    @error('montant_engage')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted" style="font-size: 0.7rem;">Montant total que le membre s'engage à payer</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="statut" class="form-label">
                        Statut <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('statut') is-invalid @enderror" 
                            id="statut" 
                            name="statut" 
                            required>
                        <option value="en_cours" {{ old('statut', 'en_cours') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="termine" {{ old('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="annule" {{ old('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                    @error('statut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="periode_debut" class="form-label">
                        Période début <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('periode_debut') is-invalid @enderror" 
                           id="periode_debut" 
                           name="periode_debut" 
                           value="{{ old('periode_debut') }}" 
                           required>
                    @error('periode_debut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="periode_fin" class="form-label">
                        Période fin <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('periode_fin') is-invalid @enderror" 
                           id="periode_fin" 
                           name="periode_fin" 
                           value="{{ old('periode_fin') }}" 
                           required>
                    @error('periode_fin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="tag" class="form-label">Tag</label>
                <select class="form-select @error('tag') is-invalid @enderror" 
                        id="tag" 
                        name="tag">
                    <option value="">-- Aucun tag --</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag }}" {{ old('tag') === $tag ? 'selected' : '' }}>{{ $tag }}</option>
                    @endforeach
                </select>
                @error('tag')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">
                    Permet de catégoriser les engagements. 
                    <a href="{{ route('engagement-tags.create') }}" target="_blank" class="text-decoration-none">
                        Créer un nouveau tag
                    </a>
                </small>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" 
                          name="notes" 
                          rows="2">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('engagements.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cotisationSelect = document.getElementById('cotisation_id');
    const montantInput = document.getElementById('montant_engage');
    const caisseInfo = document.getElementById('caisse-info');
    
    const caisseInfoInput = document.getElementById('caisse_info');
    
    cotisationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const montant = selectedOption.getAttribute('data-montant');
        const caisse = selectedOption.getAttribute('data-caisse');
        
        if (montant && montant !== 'null') {
            montantInput.value = montant;
        }
        
        if (caisse && caisse !== '-') {
            caisseInfoInput.value = caisse;
        } else {
            caisseInfoInput.value = 'Aucune caisse associée';
        }
    });
    
    // Initialiser au chargement si une cotisation est déjà sélectionnée
    if (cotisationSelect.value) {
        cotisationSelect.dispatchEvent(new Event('change'));
    }
});
</script>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Engagements
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-clipboard-check"></i> Qu'est-ce qu'un engagement ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un engagement permet à un membre de s'engager à payer un montant total sur une période donnée. Les paiements peuvent être effectués progressivement selon la périodicité définie.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-calculator"></i> Calcul automatique
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Le système calcule automatiquement le montant total selon la période (début/fin) et la périodicité. Le reste à payer est mis à jour à chaque paiement.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Périodicité
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Mensuelle, trimestrielle, semestrielle, annuelle ou unique. Cette périodicité détermine la fréquence recommandée pour les paiements de l'engagement.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

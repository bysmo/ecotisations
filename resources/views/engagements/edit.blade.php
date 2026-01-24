@extends('layouts.app')

@section('title', 'Modifier un Engagement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier l'Engagement</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations de l'Engagement
            </div>
            <div class="card-body">
        <form action="{{ route('engagements.update', $engagement) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="membre_id" class="form-label">
                        Membre <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('membre_id') is-invalid @enderror" 
                            id="membre_id" 
                            name="membre_id" 
                            required
                            disabled
                            style="background-color: #e9ecef; cursor: not-allowed;">
                        <option value="{{ $engagement->membre_id }}" selected>
                            {{ $engagement->membre->nom_complet ?? 'N/A' }} ({{ $engagement->membre->numero ?? 'N/A' }})
                        </option>
                    </select>
                    <input type="hidden" name="membre_id" value="{{ $engagement->membre_id }}">
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
                            required
                            disabled
                            style="background-color: #e9ecef; cursor: not-allowed;">
                        <option value="{{ $engagement->cotisation_id }}" selected>
                            {{ $engagement->cotisation->nom ?? 'N/A' }}
                            @if($engagement->cotisation && $engagement->cotisation->montant)
                                - {{ number_format($engagement->cotisation->montant, 0, ',', ' ') }} XOF
                            @endif
                        </option>
                    </select>
                    <input type="hidden" name="cotisation_id" value="{{ $engagement->cotisation_id }}">
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
                           style="background-color: #e9ecef; cursor: not-allowed;"
                           value="{{ $engagement->cotisation->caisse->nom ?? 'Aucune caisse associée' }}">
                    <small class="form-text text-muted" style="font-size: 0.7rem;">Caisse liée à la cotisation sélectionnée</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="periodicite" class="form-label">
                        Périodicité <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('periodicite') is-invalid @enderror" 
                            id="periodicite" 
                            name="periodicite" 
                            required
                            disabled
                            style="background-color: #e9ecef; cursor: not-allowed;">
                        <option value="{{ $engagement->periodicite ?? 'mensuelle' }}" selected>
                            {{ ucfirst($engagement->periodicite ?? 'mensuelle') }}
                        </option>
                    </select>
                    <input type="hidden" name="periodicite" value="{{ $engagement->periodicite ?? 'mensuelle' }}">
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
                    <input type="text" 
                           class="form-control" 
                           id="montant_engage" 
                           value="{{ number_format(old('montant_engage', $engagement->montant_engage), 0, ',', ' ') }} XOF" 
                           readonly
                           style="background-color: #e9ecef; cursor: not-allowed;">
                    <input type="hidden" name="montant_engage" value="{{ old('montant_engage', $engagement->montant_engage) }}">
                    @error('montant_engage')
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
                            required
                            disabled
                            style="background-color: #e9ecef; cursor: not-allowed;">
                        <option value="{{ $engagement->statut }}" selected>
                            @if($engagement->statut === 'en_cours')
                                En cours
                            @elseif($engagement->statut === 'termine')
                                Terminé
                            @else
                                Annulé
                            @endif
                        </option>
                    </select>
                    <input type="hidden" name="statut" value="{{ $engagement->statut }}">
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
                           value="{{ old('periode_debut', $engagement->periode_debut ? $engagement->periode_debut->format('Y-m-d') : '') }}" 
                           required
                           readonly
                           style="background-color: #e9ecef; cursor: not-allowed;">
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
                           value="{{ old('periode_fin', $engagement->periode_fin ? $engagement->periode_fin->format('Y-m-d') : '') }}" 
                           required
                           readonly
                           style="background-color: #e9ecef; cursor: not-allowed;">
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
                        <option value="{{ $tag }}" {{ old('tag', $engagement->tag) === $tag ? 'selected' : '' }}>{{ $tag }}</option>
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
                          rows="2"
                          readonly
                          style="background-color: #e9ecef; cursor: not-allowed;">{{ old('notes', $engagement->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('engagements.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle"></i> À propos des Engagements
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-pencil-square"></i> Modification d'un engagement
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Vous pouvez modifier le tag d'un engagement, mais les autres informations (membre, cotisation, montant, périodes, etc.) ne peuvent pas être modifiées après la création pour garantir l'intégrité des données financières.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lock"></i> Champs verrouillés
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Membre :</strong> Ne peut pas être modifié car lié aux paiements existants</li>
                    <li><strong>Cotisation :</strong> Ne peut pas être modifiée pour préserver la cohérence</li>
                    <li><strong>Montant :</strong> Ne peut pas être modifié après la création</li>
                    <li><strong>Périodes :</strong> Ne peuvent pas être modifiées après la création</li>
                    <li><strong>Statut :</strong> Géré automatiquement par le système</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Conseils
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Si vous devez modifier des informations importantes, il est recommandé de créer un nouvel engagement et d'annuler l'ancien si nécessaire.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Les champs sont en lecture seule, donc pas besoin de gestion dynamique
    // Le script est conservé pour compatibilité mais ne fait rien
});
</script>
@endsection

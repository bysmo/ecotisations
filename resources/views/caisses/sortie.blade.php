@extends('layouts.app')

@section('title', 'Nouvelle Sortie de Caisse')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-dash-square"></i> Nouvelle Sortie de Caisse</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin"></i> Enregistrer une Sortie
            </div>
            <div class="card-body">
                <form action="{{ route('caisses.sortie.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="caisse_id" class="form-label">
                                Caisse <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('caisse_id') is-invalid @enderror" 
                                    id="caisse_id" 
                                    name="caisse_id" 
                                    required>
                                <option value="">Sélectionner une caisse</option>
                                @foreach($caisses as $caisse)
                                    <option value="{{ $caisse->id }}" 
                                            data-solde="{{ $caisse->solde_actuel }}"
                                            {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                        {{ $caisse->nom }} 
                                        @if($caisse->numero)
                                            ({{ $caisse->numero }})
                                        @endif
                                        - Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} XOF
                                    </option>
                                @endforeach
                            </select>
                            @error('caisse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;" id="solde-info"></small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_sortie" class="form-label">
                                Date de sortie <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_sortie') is-invalid @enderror" 
                                   id="date_sortie" 
                                   name="date_sortie" 
                                   value="{{ old('date_sortie', date('Y-m-d')) }}" 
                                   required>
                            @error('date_sortie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="montant" class="form-label">
                                Montant (XOF) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('montant') is-invalid @enderror" 
                                   id="montant" 
                                   name="montant" 
                                   value="{{ old('montant') }}" 
                                   min="1" 
                                   step="1" 
                                   required>
                            @error('montant')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="motif" class="form-label">Motif</label>
                            <input type="text" 
                                   class="form-control @error('motif') is-invalid @enderror" 
                                   id="motif" 
                                   name="motif" 
                                   value="{{ old('motif') }}" 
                                   maxlength="255">
                            @error('motif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Raison de la sortie (ex: Dépense, Achat, etc.)</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('caisses.sortie') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer la sortie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Sorties
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-dash-square"></i> Qu'est-ce qu'une sortie ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Une sortie de caisse enregistre une dépense ou un retrait de fonds. Cette opération débite le solde de la caisse et doit être justifiée par un motif.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Vérifications
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Le solde de la caisse doit être suffisant</li>
                    <li>Le motif est obligatoire pour la traçabilité</li>
                    <li>Un montant minimal de 1 XOF est requis</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Bonnes pratiques
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Utilisez les notes pour ajouter des détails supplémentaires sur la dépense (fournisseur, numéro de facture, etc.). Cela facilite le suivi et l'audit.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const caisseSelect = document.getElementById('caisse_id');
        const soldeInfo = document.getElementById('solde-info');
        
        caisseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const solde = selectedOption.getAttribute('data-solde');
            
            if (solde && solde !== 'null') {
                soldeInfo.textContent = 'Solde disponible: ' + parseFloat(solde).toLocaleString('fr-FR') + ' XOF';
            } else {
                soldeInfo.textContent = '';
            }
        });
        
        // Initialiser au chargement si une caisse est déjà sélectionnée
        if (caisseSelect.value) {
            caisseSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection

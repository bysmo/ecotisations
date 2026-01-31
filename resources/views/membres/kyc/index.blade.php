@extends('layouts.membre')

@section('title', 'Mon KYC')

@push('styles')
<style>
    .kyc-page,
    .kyc-page .form-label,
    .kyc-page .form-control,
    .kyc-page .form-select,
    .kyc-page .btn,
    .kyc-page .card-body,
    .kyc-page .card-header,
    .kyc-page .page-header h1,
    .kyc-page .alert {
        font-family: 'Ubuntu', sans-serif !important;
        font-weight: 300 !important;
    }
    .kyc-page .form-label { font-size: 0.8rem; color: #333; margin-bottom: 0.35rem; }
    .kyc-page .form-control,
    .kyc-page .form-select { font-size: 0.8rem; }
    .kyc-page .card-header { font-size: 0.75rem; }
    .kyc-page .page-header h1 { font-size: 1.25rem; }
</style>
@endpush

@section('content')
<div class="kyc-page">
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Mon KYC</h1>
</div>

<div class="row">
    <div class="col-md-8">
@if(!$kyc || $kyc->isRejete())
    {{-- Formulaire de soumission KYC (pas encore soumis ou rejeté) --}}
    @if($kyc && $kyc->isRejete())
        <div class="alert alert-warning">
            <strong>Votre KYC a été rejeté.</strong>
            @if($kyc->motif_rejet)
                <p class="mb-0 mt-2">Motif : {{ $kyc->motif_rejet }}</p>
            @endif
            <p class="mb-0 mt-2">Vous pouvez soumettre à nouveau votre dossier ci-dessous.</p>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="bi bi-info-circle"></i> Soumettre mon dossier KYC
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Renseignez vos informations et joignez les documents demandés. Le KYC doit être validé avant de pouvoir faire une demande de nano crédit.</p>
            <form action="{{ route('membre.kyc.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type_piece" class="form-label">Type de pièce d'identité <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_piece') is-invalid @enderror" id="type_piece" name="type_piece" required>
                            <option value="">-- Choisir --</option>
                            <option value="cni" {{ old('type_piece', $kyc->type_piece ?? '') === 'cni' ? 'selected' : '' }}>CNI</option>
                            <option value="passeport" {{ old('type_piece', $kyc->type_piece ?? '') === 'passeport' ? 'selected' : '' }}>Passeport</option>
                            <option value="permis" {{ old('type_piece', $kyc->type_piece ?? '') === 'permis' ? 'selected' : '' }}>Permis</option>
                        </select>
                        @error('type_piece')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="numero_piece" class="form-label">Numéro de la pièce <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_piece') is-invalid @enderror" id="numero_piece" name="numero_piece" value="{{ old('numero_piece', $kyc->numero_piece ?? '') }}" required>
                        @error('numero_piece')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $kyc ? $kyc->date_naissance?->format('Y-m-d') : '') }}" required>
                        @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lieu_naissance" class="form-label">Lieu de naissance <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $kyc->lieu_naissance ?? '') }}" required>
                        @error('lieu_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="adresse_kyc" class="form-label">Adresse complète (KYC) <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('adresse_kyc') is-invalid @enderror" id="adresse_kyc" name="adresse_kyc" rows="3" required>{{ old('adresse_kyc', $kyc->adresse_kyc ?? '') }}</textarea>
                    @error('adresse_kyc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="metier" class="form-label">Métier</label>
                        <input type="text" class="form-control @error('metier') is-invalid @enderror" id="metier" name="metier" value="{{ old('metier', $kyc->metier ?? '') }}" placeholder="Ex : Commerçant, Enseignant">
                        @error('metier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="localisation" class="form-label">Localisation</label>
                        <input type="text" class="form-control @error('localisation') is-invalid @enderror" id="localisation" name="localisation" value="{{ old('localisation', $kyc->localisation ?? '') }}" placeholder="Ex : Dakar, Thiès">
                        @error('localisation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact_1" class="form-label">Contact 1</label>
                        <input type="text" class="form-control @error('contact_1') is-invalid @enderror" id="contact_1" name="contact_1" value="{{ old('contact_1', $kyc->contact_1 ?? '') }}" placeholder="Téléphone ou email">
                        @error('contact_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contact_2" class="form-label">Contact 2</label>
                        <input type="text" class="form-control @error('contact_2') is-invalid @enderror" id="contact_2" name="contact_2" value="{{ old('contact_2', $kyc->contact_2 ?? '') }}" placeholder="Téléphone ou email">
                        @error('contact_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                {{-- Pièce d'identité : recto et verso --}}
                <div class="mb-3">
                    <label class="form-label">Pièce d'identité (recto et verso) <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="piece_identite_recto" class="form-label small text-muted">Recto</label>
                            <input type="file" class="form-control @error('piece_identite_recto') is-invalid @enderror" id="piece_identite_recto" name="piece_identite_recto" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF ou image, max 5 Mo</small>
                            @error('piece_identite_recto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="piece_identite_verso" class="form-label small text-muted">Verso</label>
                            <input type="file" class="form-control @error('piece_identite_verso') is-invalid @enderror" id="piece_identite_verso" name="piece_identite_verso" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF ou image, max 5 Mo</small>
                            @error('piece_identite_verso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="photo_identite" class="form-label">Photo d'identité <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('photo_identite') is-invalid @enderror" id="photo_identite" name="photo_identite" accept=".jpg,.jpeg,.png" required>
                        <small class="text-muted">JPG/PNG, max 5 Mo. Visage bien visible.</small>
                        @error('photo_identite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="justificatif_domicile" class="form-label">Justificatif de domicile <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('justificatif_domicile') is-invalid @enderror" id="justificatif_domicile" name="justificatif_domicile" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">PDF ou image, max 5 Mo</small>
                        @error('justificatif_domicile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Soumettre mon KYC
                </button>
            </form>
        </div>
    </div>
@elseif($kyc->isEnAttente())
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clock-history"></i> Statut KYC
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <i class="bi bi-hourglass-split"></i> <strong>En attente de validation</strong><br>
                Votre dossier KYC a bien été reçu et est en cours d'examen par l'administration. Vous serez notifié dès qu'une décision sera prise.
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header">
            <i class="bi bi-check-circle"></i> Statut KYC
        </div>
        <div class="card-body">
            <div class="alert alert-success mb-0">
                <i class="bi bi-shield-check"></i> <strong>KYC validé</strong><br>
                Votre identité a été vérifiée. Vous pouvez effectuer une demande de nano crédit.
            </div>
        </div>
    </div>
@endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos du KYC
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Qu'est-ce que le KYC ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Le KYC (Know Your Customer) permet de vérifier votre identité. Un dossier validé est nécessaire avant toute demande de nano crédit.
                </p>
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-file-earmark-image"></i> Documents demandés
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Pièce d'identité :</strong> recto et verso (CNI, passeport ou permis)</li>
                    <li><strong>Photo d'identité :</strong> photo récente, visage bien visible</li>
                    <li><strong>Justificatif de domicile :</strong> facture, attestation de moins de 3 mois</li>
                </ul>
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Conseils
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Utilisez des documents lisibles (PDF ou photo nette). Après soumission, votre dossier sera examiné par l'administration ; en cas de rejet, un motif vous sera communiqué et vous pourrez soumettre à nouveau.
                </p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

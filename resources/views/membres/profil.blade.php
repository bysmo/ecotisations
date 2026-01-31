@extends('layouts.membre')

@section('title', 'Mes Infos Personnelles')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="bi bi-person-vcard"></i> Mes Infos Personnelles</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('membre.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Sidebar: Profile Summary -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header text-center py-4" style="background: linear-gradient(135deg, var(--primary-dark-blue) 0%, #2c5282 100%);">
                <div class="position-relative d-inline-block">
                    @if($membre->kycVerification && $membre->kycVerification->selfie)
                        <img src="{{ asset('storage/' . $membre->kycVerification->selfie) }}" 
                             alt="Profile" 
                             class="rounded-circle border border-4 border-white shadow-sm" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle border border-4 border-white shadow-sm bg-light d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle shadow-sm" title="Actif"></span>
                </div>
                <h4 class="text-white mt-3 mb-1" style="font-weight: 500;">{{ $membre->nom_complet }}</h4>
                <p class="text-white-50 small mb-0">{{ $membre->numero }}</p>
                <div class="mt-2">
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-tag-fill me-1"></i> {{ $membre->segment ?? 'Standard' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2"></i> Adhésion</span>
                        <span class="fw-medium">{{ $membre->date_adhesion->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="bi bi-shield-lock me-2"></i> Statut KYC</span>
                        @if($membre->hasKycValide())
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Validé</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">En attente</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-0">
                        <span class="text-muted"><i class="bi bi-envelope me-2"></i> Email</span>
                        <span class="small">{{ $membre->email }}</span>
                    </li>
                </ul>
            </div>
            @if(!$membre->hasKycValide())
            <div class="card-footer bg-light border-0 p-3">
                <a href="{{ route('membre.kyc.index') }}" class="btn btn-primary w-100 shadow-sm">
                    <i class="bi bi-shield-exclamation me-2"></i> Finaliser mon KYC
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Section: Form -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 text-primary" style="font-weight: 500;">
                    <i class="bi bi-pencil-square me-2"></i> Modifier mes informations
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('membre.profil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: État Civil -->
                    <div class="section-title mb-4">
                        <h6 class="text-muted fw-bold text-uppercase small"><i class="bi bi-person-lines-fill me-2"></i> État Civil</h6>
                        <hr class="mt-2 mb-4 opacity-10">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-medium">Nom</label>
                            <input type="text" class="form-control bg-light-subtle" value="{{ $membre->nom }}" disabled readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-medium">Prénom</label>
                            <input type="text" class="form-control bg-light-subtle" value="{{ $membre->prenom }}" disabled readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label text-muted small fw-medium">Date de naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance" 
                                   class="form-control @error('date_naissance') is-invalid @enderror" 
                                   value="{{ old('date_naissance', $membre->date_naissance ? $membre->date_naissance->format('Y-m-d') : '') }}">
                            @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="lieu_naissance" class="form-label text-muted small fw-medium">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" id="lieu_naissance" 
                                   class="form-control @error('lieu_naissance') is-invalid @enderror" 
                                   placeholder="Ville de naissance"
                                   value="{{ old('lieu_naissance', $membre->lieu_naissance) }}">
                            @error('lieu_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sexe" class="form-label text-muted small fw-medium">Sexe</label>
                            <select name="sexe" id="sexe" class="form-select @error('sexe') is-invalid @enderror">
                                <option value="" disabled selected>Sélectionner...</option>
                                <option value="M" {{ old('sexe', $membre->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                <option value="F" {{ old('sexe', $membre->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                            </select>
                            @error('sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nom_mere" class="form-label text-muted small fw-medium">Nom de la mère</label>
                            <input type="text" name="nom_mere" id="nom_mere" 
                                   class="form-control @error('nom_mere') is-invalid @enderror" 
                                   placeholder="Nom complet"
                                   value="{{ old('nom_mere', $membre->nom_mere) }}">
                            @error('nom_mere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Section 2: Coordonnées -->
                    <div class="section-title mt-5 mb-4">
                        <h6 class="text-muted fw-bold text-uppercase small"><i class="bi bi-geo-alt-fill me-2"></i> Coordonnées & Contact</h6>
                        <hr class="mt-2 mb-4 opacity-10">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label text-muted small fw-medium">Adresse Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $membre->email) }}" required>
                            </div>
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label text-muted small fw-medium">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="text" name="telephone" id="telephone" 
                                       class="form-control border-start-0 @error('telephone') is-invalid @enderror" 
                                       value="{{ old('telephone', $membre->telephone) }}">
                            </div>
                            @error('telephone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="adresse" class="form-label text-muted small fw-medium">Adresse physique</label>
                            <textarea name="adresse" id="adresse" rows="2" 
                                      class="form-control @error('adresse') is-invalid @enderror" 
                                      placeholder="Votre adresse actuelle">{{ old('adresse', $membre->adresse) }}</textarea>
                            @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Section 3: Sécurité -->
                    <div class="section-title mt-5 mb-4">
                        <h6 class="text-muted fw-bold text-uppercase small"><i class="bi bi-lock-fill me-2"></i> Sécurité du compte</h6>
                        <hr class="mt-2 mb-4 opacity-10">
                        <div class="alert alert-info py-2 px-3 border-0 rounded-3 d-flex align-items-center mb-4 shadow-none">
                            <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                            <span class="small">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label text-muted small fw-medium">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" id="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       placeholder="Minimum 6 caractères">
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label text-muted small fw-medium">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control border-start-0" 
                                       placeholder="À l'identique">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                            <i class="bi bi-check2-circle me-2"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "\F138";
        font-family: "bootstrap-icons";
        font-size: 0.7rem;
        vertical-align: middle;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(44, 82, 130, 0.1);
    }
    .section-title h6 {
        letter-spacing: 0.5px;
        color: var(--primary-dark-blue) !important;
    }
    .card-title {
        font-family: 'Ubuntu', sans-serif;
    }
</style>
@endpush

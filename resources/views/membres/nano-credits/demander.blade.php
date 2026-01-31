@extends('layouts.membre')

@section('title', 'Demander un nano crédit - ' . $type->nom)

@section('content')
<style>
    .page-nano-demander,
    .page-nano-demander .card,
    .page-nano-demander .form-label,
    .page-nano-demander .form-control,
    .page-nano-demander .form-select,
    .page-nano-demander .btn,
    .page-nano-demander .card-header,
    .page-nano-demander .card-body,
    .page-nano-demander small,
    .page-nano-demander p {
        font-family: 'Ubuntu', sans-serif !important;
        font-weight: 300 !important;
    }
    .page-nano-demander .card-header {
        font-size: 0.9rem;
        color: var(--primary-dark-blue);
    }
    .page-nano-demander .form-label {
        font-size: 0.85rem;
        color: var(--primary-dark-blue);
    }
    .page-nano-demander .form-control,
    .page-nano-demander .form-select {
        font-size: 0.9rem;
        font-weight: 300;
    }
</style>

<div class="page-header page-nano-demander">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-send"></i> Demander un nano crédit — {{ $type->nom }}
    </h1>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row page-nano-demander">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle"></i> Votre demande</div>
            <div class="card-body">
                <p class="small text-muted mb-3">Choisissez uniquement le montant dont vous avez besoin. Une fois votre demande enregistrée, l'administration l'étudiera (dont votre KYC) et vous octroiera le crédit sur le numéro de mobile money de votre profil si accordé.</p>
                <form action="{{ route('membre.nano-credits.demander.store', $type) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant demandé (XOF) <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant', $type->montant_min) }}" required min="{{ (int) $type->montant_min }}" @if($type->montant_max) max="{{ (int) $type->montant_max }}" @endif style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <small class="form-text text-muted">Entre {{ number_format($type->montant_min, 0, ',', ' ') }} et {{ $type->montant_max ? number_format($type->montant_max, 0, ',', ' ') : 'illimité' }} XOF</small>
                        @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><i class="bi bi-send"></i> Envoyer ma demande</button>
                        <a href="{{ route('membre.nano-credits') }}" class="btn btn-outline-secondary" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light page-nano-demander">
            <div class="card-header"><i class="bi bi-info-circle"></i> Récapitulatif type</div>
            <div class="card-body small">
                <p class="mb-1"><strong>{{ $type->nom }}</strong></p>
                <p class="mb-1">Durée : {{ $type->duree_mois }} mois</p>
                <p class="mb-1">Taux : {{ number_format($type->taux_interet, 1, ',', ' ') }} % / an</p>
                <p class="mb-0">Remboursement : {{ $type->frequence_remboursement_label }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

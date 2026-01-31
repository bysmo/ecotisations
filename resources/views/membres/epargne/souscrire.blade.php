@extends('layouts.membre')

@section('title', 'Souscrire - ' . $plan->nom)

@section('content')
<div class="page-header">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-plus-circle"></i> Souscrire au plan « {{ $plan->nom }} »
    </h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <i class="bi bi-pencil-square"></i> Vos choix
            </div>
            <div class="card-body">
                <form action="{{ route('membre.epargne.souscrire.store', $plan) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant par versement (XOF) <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="{{ $plan->montant_min }}" @if($plan->montant_max) max="{{ $plan->montant_max }}" @endif
                               class="form-control @error('montant') is-invalid @enderror"
                               id="montant" name="montant" value="{{ old('montant', $plan->montant_min) }}" required>
                        <small class="text-muted">Min. {{ number_format($plan->montant_min, 0, ',', ' ') }} XOF @if($plan->montant_max) – Max. {{ number_format($plan->montant_max, 0, ',', ' ') }} XOF @endif</small>
                        @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                               id="date_debut" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required min="{{ now()->format('Y-m-d') }}">
                        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if($plan->frequence === 'mensuel')
                        <div class="mb-3">
                            <label for="jour_du_mois" class="form-label">Jour du mois (1–28) <span class="text-danger">*</span></label>
                            <select class="form-select @error('jour_du_mois') is-invalid @enderror" id="jour_du_mois" name="jour_du_mois" required>
                                @for($j = 1; $j <= 28; $j++)
                                    <option value="{{ $j }}" {{ old('jour_du_mois', 1) == $j ? 'selected' : '' }}>{{ $j }} {{ $j == 1 ? 'er' : '' }} du mois</option>
                                @endfor
                            </select>
                            @error('jour_du_mois')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('membre.epargne.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirmer la souscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header" style="font-weight: 300;"><i class="bi bi-info-circle"></i> Récapitulatif</div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ $plan->nom }}</strong></p>
                <p class="small text-muted mb-2">{{ $plan->description }}</p>
                <ul class="list-unstyled small mb-2">
                    <li><i class="bi bi-arrow-repeat me-1"></i> {{ $plan->frequence_label }}</li>
                    <li><i class="bi bi-percent me-1"></i> Taux : {{ number_format($plan->taux_remuneration ?? 0, 1, ',', ' ') }} % / an</li>
                    <li><i class="bi bi-calendar-range me-1"></i> Durée : {{ $plan->duree_mois ?? 12 }} mois</li>
                </ul>
                <hr class="my-2">
                <p class="small fw-bold mb-1">Pour {{ number_format($exempleCalcul['nombre_versements'] ?? 0, 0, ',', ' ') }} versements de {{ number_format(old('montant', $plan->montant_min), 0, ',', ' ') }} XOF :</p>
                <ul class="list-unstyled small mb-1">
                    <li>Date de fin prévue : <strong>{{ $dateFinExemple }}</strong></li>
                    <li>Total épargné : {{ number_format($exempleCalcul['montant_total_verse'] ?? 0, 0, ',', ' ') }} XOF</li>
                    <li>Rémunération ({{ number_format($plan->taux_remuneration ?? 0, 1, ',', ' ') }} %) : {{ number_format($exempleCalcul['remuneration'] ?? 0, 0, ',', ' ') }} XOF</li>
                </ul>
                <p class="small mb-0 mt-2 p-2 rounded" style="background: #e7f3ff;">
                    <i class="bi bi-bank me-1"></i> <strong>Montant total reversé à l'échéance : {{ number_format($exempleCalcul['montant_total_reverse'] ?? 0, 0, ',', ' ') }} XOF</strong>
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.7rem;">Ces montants sont indicatifs selon le montant et la date de début choisis.</p>
            </div>
        </div>
    </div>
</div>
@endsection

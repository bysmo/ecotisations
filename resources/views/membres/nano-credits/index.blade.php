@extends('layouts.membre')

@section('title', 'Nano crédit')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-phone"></i> Nano crédit
    </h1>
    <a href="{{ route('membre.nano-credits.mes') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-wallet2"></i> Mes nano crédits
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-list-ul"></i> Types de nano crédit disponibles
    </div>
    <div class="card-body">
        @if($types->count() > 0)
            <p class="text-muted small mb-3">Choisissez un type et soumettez une demande. L'administration étudiera votre dossier (KYC) puis octroiera le crédit sur votre mobile money si accordé.</p>
            <div class="row g-3">
                @foreach($types as $type)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border shadow-sm" style="border-radius: 8px;">
                            <div class="card-body">
                                <h6 class="card-title" style="font-weight: 400; color: var(--primary-dark-blue);">
                                    {{ $type->nom }}
                                </h6>
                                @if($type->description)
                                    <p class="card-text small text-muted mb-2" style="font-size: 0.75rem;">{{ Str::limit($type->description, 100) }}</p>
                                @endif
                                <ul class="list-unstyled small mb-3" style="font-size: 0.75rem;">
                                    <li><i class="bi bi-cash-coin me-1"></i> {{ number_format($type->montant_min, 0, ',', ' ') }} – {{ $type->montant_max ? number_format($type->montant_max, 0, ',', ' ') . ' XOF' : 'illimité' }}</li>
                                    <li><i class="bi bi-percent me-1"></i> Taux : {{ number_format($type->taux_interet ?? 0, 1, ',', ' ') }} % / an</li>
                                    <li><i class="bi bi-calendar-range me-1"></i> Durée : {{ $type->duree_mois }} mois</li>
                                    <li><i class="bi bi-arrow-repeat me-1"></i> Remboursement : {{ $type->frequence_remboursement_label }}</li>
                                </ul>
                                <a href="{{ route('membre.nano-credits.demander', $type) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-send"></i> Demander un nano crédit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-phone text-muted" style="font-size: 2.5rem;"></i>
                <p class="text-muted mt-2 mb-0">Aucun type de nano crédit disponible pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

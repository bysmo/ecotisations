@extends('layouts.membre')

@section('title', 'Détails du compte')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-wallet2 me-2"></i>Détails : {{ $compte->nom }}
    </h1>
    <a href="{{ route('membre.comptes') }}" class="btn btn-sm btn-outline-secondary px-3 shadow-sm" style="border-radius: 20px; font-weight: 400;">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold mb-4 small text-uppercase" style="letter-spacing: 1px;">Information d'identité</h6>
                
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">NUMÉRO DE COMPTE (INTERNE)</label>
                        <div class="fw-bold text-dark px-3 py-2 bg-light rounded" style="font-family: monospace; font-size: 1rem; border-left: 3px solid var(--primary-dark-blue);">
                            {{ $compte->numero }}
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">NUMÉRO DE COMPTE BANCAIRE (CORE BANKING)</label>
                        <div class="fw-bold text-primary px-3 py-2 bg-light rounded" style="font-family: monospace; font-size: 1rem; border-left: 3px solid var(--light-blue);">
                            {{ $compte->numero_core_banking ?? 'NON DÉFINI' }}
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">TYPE DE COMPTE</label>
                        <div>
                            @switch($compte->type)
                                @case('courant') <span class="badge bg-primary px-2 py-1 w-100" style="font-weight: 400;">COURANT</span> @break
                                @case('epargne') <span class="badge bg-success px-2 py-1 w-100" style="font-weight: 400;">ÉPARGNE</span> @break
                                @case('tontine') <span class="badge bg-info px-2 py-1 w-100" style="font-weight: 400;">TONTINE</span> @break
                                @case('nano_credit') <span class="badge bg-warning text-dark px-2 py-1 w-100" style="font-weight: 400;">NANO-CRÉDIT</span> @break
                                @default <span class="badge bg-secondary px-2 py-1 w-100" style="font-weight: 400;">{{ strtoupper($compte->type) }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">ÉTAT ACTUEL</label>
                        <div>
                            @if($compte->isActive())
                                <span class="badge bg-success rounded-pill px-2 py-1 w-100" style="font-weight: 400;"><i class="bi bi-check-circle me-1"></i>ACTIF</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-2 py-1 w-100" style="font-weight: 400;"><i class="bi bi-x-circle me-1"></i>INACTIF</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden" style="border-radius: 12px; background: linear-gradient(135deg, var(--primary-dark-blue), var(--primary-blue));">
            <!-- Décoration en arrière-plan -->
            <div class="position-absolute opacity-10" style="right: -20px; bottom: -30px; font-size: 15rem; transform: rotate(-15deg);">
                <i class="bi bi-cash-stack text-white"></i>
            </div>
            
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-white p-5 position-relative">
                <div class="text-center">
                    <h6 class="fw-light mb-3 opacity-75 text-uppercase" style="letter-spacing: 2px; font-size: 0.8rem;">Solde Actuel Disponible</h6>
                    <h1 class="display-3 fw-bold mb-2" style="font-family: 'Ubuntu', sans-serif;">
                        {{ number_format($compte->solde_actuel, 0, ',', ' ') }}
                        <span style="font-size: 0.4em; font-weight: 300; vertical-align: middle;" class="ms-1">XOF</span>
                    </h1>
                    <div class="px-3 py-1 rounded-pill d-inline-block" style="background: rgba(255,255,255,0.1); font-size: 0.75rem; border: 1px solid rgba(255,255,255,0.2);">
                        Dernière mise à jour le {{ now()->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
        <h6 class="mb-0 fw-bold" style="color: var(--primary-dark-blue); font-family: 'Ubuntu', sans-serif;">
            <i class="bi bi-clock-history me-2"></i>Historique des mouvements
        </h6>
        @if($mouvements->total() > 0)
            <span class="badge bg-light text-muted border fw-normal">{{ $mouvements->total() }} opération(s)</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background-color: var(--primary-dark-blue); color: white;">
                        <th class="ps-4 py-3 border-0 text-white small text-uppercase" style="width: 150px; font-weight: 500;">Date & Heure</th>
                        <th class="py-3 border-0 text-white small text-uppercase" style="font-weight: 500;">Libellé / Détails</th>
                        <th class="py-3 border-0 text-white small text-uppercase text-center" style="width: 120px; font-weight: 500;">Sens</th>
                        <th class="py-3 border-0 text-white small text-uppercase text-end" style="width: 150px; font-weight: 500;">Montant</th>
                        <th class="pe-4 py-3 border-0 text-white small text-uppercase text-end" style="width: 200px; font-weight: 500;">Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $mouvement)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold" style="color: var(--primary-dark-blue);">{{ $mouvement->date_operation->format('d M Y') }}</div>
                            <small class="text-muted">{{ $mouvement->date_operation->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary-dark-blue);">{{ $mouvement->libelle }}</div>
                            @if($mouvement->notes)
                                <div class="text-muted mb-0" style="font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;">
                                    {{ $mouvement->notes }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($mouvement->isEntree())
                                <span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 0.65rem; border: 1px solid rgba(25, 135, 84, 0.2);">ENTRÉE</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 py-1" style="font-size: 0.65rem; border: 1px solid rgba(220, 53, 69, 0.2);">SORTIE</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $mouvement->isEntree() ? 'text-success' : 'text-danger' }}" style="font-size: 1rem;">
                            {{ $mouvement->isEntree() ? '+' : '-' }} {{ number_format($mouvement->montant, 0, ',', ' ') }}
                        </td>
                        <td class="pe-4 text-end">
                            <span class="text-muted small" style="font-family: monospace;">
                                {{ strtoupper(str_replace('_', ' ', $mouvement->reference_type ?? 'SYSTÈME')) }} #{{ $mouvement->reference_id ?? 'AUTO' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">
                            <div class="py-4">
                                <i class="bi bi-clock mb-3" style="font-size: 3rem; opacity: 0.1; display: block;"></i>
                                Aucun mouvement enregistré sur ce compte pour le moment.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mouvements->hasPages())
        <div class="card-footer bg-white py-3 border-0 border-top" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
            <div class="pagination-custom d-flex justify-content-end">
                {{ $mouvements->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endsection

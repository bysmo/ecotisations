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
                
                <div class="mb-4">
                    <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">NUMÉRO DE COMPTE</label>
                    <div class="fw-bold text-dark px-3 py-2 bg-light rounded" style="font-family: monospace; font-size: 1.1rem; border-left: 3px solid var(--primary-dark-blue);">
                        {{ $compte->numero }}
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted small display-block mb-1" style="font-size: 0.7rem;">TYPE DE COMPTE</label>
                        <div>
                            @switch($compte->type)
                                @case('courant') <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 w-100">COURANT</span> @break
                                @case('epargne') <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 w-100">ÉPARGNE</span> @break
                                @case('tontine') <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 w-100">TONTINE</span> @break
                                @case('nano_credit') <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 w-100">NANO-CRÉDIT</span> @break
                                @default <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 w-100">{{ strtoupper($compte->type) }}</span>
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
                    <tr class="bg-light">
                        <th class="ps-4 py-3 border-0 text-muted small text-uppercase" style="width: 150px;">Date & Heure</th>
                        <th class="py-3 border-0 text-muted small text-uppercase">Libellé / Détails</th>
                        <th class="py-3 border-0 text-muted small text-uppercase text-center" style="width: 120px;">Sens</th>
                        <th class="py-3 border-0 text-muted small text-uppercase text-end" style="width: 150px;">Montant</th>
                        <th class="pe-4 py-3 border-0 text-muted small text-uppercase text-end" style="width: 200px;">Référence</th>
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

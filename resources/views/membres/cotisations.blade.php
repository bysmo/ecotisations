@extends('layouts.membre')

@section('title', 'Mes Cotisations')

@section('content')
<div class="page-header">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><i class="bi bi-receipt-cutoff"></i> Mes Cotisations</h1>
</div>

<div class="card">
    <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-list-ul"></i> Cotisations Disponibles
    </div>
    <div class="card-body">
        @if($cotisations->count() > 0)
            <style>
                .table-cotisations-membre {
                    margin-bottom: 0;
                }
                .table-cotisations-membre thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: #ffffff !important;
                    background-color: var(--primary-dark-blue) !important;
                    border-bottom: 2px solid #dee2e6 !important;
                }
                .table-cotisations-membre tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-cotisations-membre tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-cotisations-membre.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-cotisations-membre.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-cotisations-membre.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-cotisations-membre.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-cotisations-membre .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-cotisations-membre .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-cotisations-membre .btn-group-sm > .btn,
                .table-cotisations-membre .btn-group > .btn {
                    border-radius: 0.2rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-cotisations-membre table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Tag</th>
                            <th>Type</th>
                            <th>Fréquence</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cotisations as $cotisation)
                            <tr>
                                <td>
                                    {{ $cotisation->nom }}
                                </td>
                                <td>
                                    {{ $cotisation->tag ?? '-' }}
                                </td>
                                <td>
                                    {{ ucfirst($cotisation->type ?? 'N/A') }}
                                </td>
                                <td>
                                    {{ $cotisation->frequence ? ucfirst($cotisation->frequence) : '-' }}
                                </td>
                                <td>
                                    {{ number_format($cotisation->montant, 0, ',', ' ') }} XOF
                                </td>
                                <td>
                                    @if($cotisation->actif)
                                        <span style="color: #28a745; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Active</span>
                                    @else
                                        <span style="color: #dc3545; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($cotisation->description ?? 'Aucune description', 50) }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('membre.cotisations.show', $cotisation->id) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($paydunyaEnabled && $cotisation->actif)
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    onclick="initierPaiementPayDunya({{ $cotisation->id }}, '{{ $cotisation->nom }}', {{ $cotisation->montant }})"
                                                    title="Payer via PayDunya">
                                                <i class="bi bi-phone"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($cotisations->hasPages() || $cotisations->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $cotisations->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Aucune cotisation disponible</p>
            </div>
        @endif
    </div>
</div>




@if($paydunyaEnabled)
<script>
function initierPaiementPayDunya(cotisationId, nomCotisation, montant) {
    if (confirm('Voulez-vous payer la cotisation "' + nomCotisation + '" d\'un montant de ' + new Intl.NumberFormat('fr-FR').format(montant) + ' XOF via PayDunya ?')) {
        // Rediriger vers la page de détails pour initier le paiement
        window.location.href = '{{ route("membre.cotisations.show", ":id") }}'.replace(':id', cotisationId) + '?init_payment=1';
    }
}
</script>
@endif
@endsection

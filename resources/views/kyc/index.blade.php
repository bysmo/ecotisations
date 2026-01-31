@extends('layouts.app')

@section('title', 'KYC - Vérifications')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> KYC - Vérifications</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Liste des dossiers KYC
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('kyc.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-8">
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Rechercher par nom, prénom, email ou numéro membre..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </div>
            @if(request('search') || request('statut'))
                <div class="mt-2">
                    <a href="{{ route('kyc.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>

        @if($kycs->count() > 0)
            <style>
                .table-kyc thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-kyc tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-kyc .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-kyc .btn i {
                    font-size: 0.65rem !important;
                }
                .table-kyc tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-kyc.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-kyc.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-kyc.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                }
                table.table.table-kyc.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-kyc table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Numéro</th>
                            <th>Type pièce</th>
                            <th>Date soumission</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kycs as $k)
                            <tr>
                                <td>{{ $k->membre->nom_complet ?? '-' }}</td>
                                <td>{{ $k->membre->numero ?? '-' }}</td>
                                <td>{{ ucfirst($k->type_piece ?? '-') }}</td>
                                <td>{{ $k->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($k->statut === 'en_attente')
                                        En attente
                                    @elseif($k->statut === 'valide')
                                        Validé
                                    @else
                                        Rejeté
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kyc.show', $k) }}" class="btn btn-outline-primary btn-sm" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">
                {{ $kycs->links() }}
            </div>
        @else
            <p class="text-muted mb-0">Aucun dossier KYC trouvé.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Types de nano crédit')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1><i class="bi bi-phone"></i> Types de nano crédit</h1>
    <a href="{{ route('nano-credit-types.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Nouveau type
    </a>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-ul"></i> Liste des types</div>
    <div class="card-body">
        <form method="GET" action="{{ route('nano-credit-types.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nom ou description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="actif" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                        <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Filtrer</button>
                </div>
            </div>
        </form>

        @if($types->count() > 0)
            <style>
                .table-nano-types thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-nano-types tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-nano-types .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-nano-types .btn i { font-size: 0.6rem !important; }
                .table-nano-types tbody tr:last-child td { border-bottom: none !important; }
                table.table.table-nano-types.table-hover tbody tr { background-color: #ffffff !important; }
                table.table.table-nano-types.table-hover tbody tr:nth-child(even) { background-color: #d4dde8 !important; }
                table.table.table-nano-types.table-hover tbody tr:hover { background-color: #b8c7d9 !important; cursor: pointer !important; }
            </style>
            <div class="table-responsive">
                <table class="table table-nano-types table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th class="text-end">Montant min</th>
                            <th class="text-end">Montant max</th>
                            <th class="text-center">Durée (mois)</th>
                            <th class="text-center">Taux %</th>
                            <th>Fréquence remb.</th>
                            <th class="text-center">Demandes</th>
                            <th class="text-center">Actif</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                            <tr>
                                <td>{{ $type->nom }}</td>
                                <td class="text-end">{{ number_format($type->montant_min, 0, ',', ' ') }} XOF</td>
                                <td class="text-end">{{ $type->montant_max ? number_format($type->montant_max, 0, ',', ' ') . ' XOF' : '—' }}</td>
                                <td class="text-center">{{ $type->duree_mois }}</td>
                                <td class="text-center">{{ number_format($type->taux_interet, 1, ',', ' ') }} %</td>
                                <td>{{ $type->frequence_remboursement_label }}</td>
                                <td class="text-center">{{ $type->demandes_count ?? 0 }}</td>
                                <td class="text-center">{{ $type->actif ? 'Oui' : 'Non' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('nano-credit-types.edit', $type) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('nano-credit-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce type ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Aucun type de nano crédit. Créez-en un pour que les membres puissent souscrire.</p>
        @endif
    </div>
</div>
@endsection

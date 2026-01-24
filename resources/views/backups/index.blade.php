@extends('layouts.app')

@section('title', 'Backup et Restauration')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-database"></i> Backup et Restauration</h1>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-plus-circle"></i> Créer un Backup</span>
        <form action="{{ route('backups.create') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-database-check"></i> Créer un Backup
            </button>
        </form>
    </div>
    <div class="card-body">
        <p class="text-muted mb-0" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
            Créez une copie de sauvegarde de votre base de données. Les backups sont stockés dans le répertoire de stockage de l'application.
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Backups disponibles ({{ count($backups) }})
    </div>
    <div class="card-body">
        @if(count($backups) > 0)
            <style>
                .table-backups thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: white !important;
                    background-color: var(--primary-dark-blue) !important;
                }
                .table-backups tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-backups tbody tr:last-child td {
                    border-bottom: none !important;
                }
                .table-backups .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-backups .btn i {
                    font-size: 0.6rem !important;
                }
                table.table.table-backups.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-backups.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-backups.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-backups.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-backups code {
                    font-size: 0.6rem !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-backups table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom du fichier</th>
                            <th>Taille</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><code>{{ $backup['name'] }}</code></td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $backup['date'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('backups.download', $backup['name']) }}" class="btn btn-outline-primary" title="Télécharger">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('backups.restore', $backup['name']) }}" method="POST" class="d-inline" onsubmit="return confirmAction('Êtes-vous sûr de vouloir restaurer ce backup ? Cette action remplacera toutes les données actuelles.');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Restaurer">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST" class="d-inline" onsubmit="return confirmAction('Êtes-vous sûr de vouloir supprimer ce backup ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center mb-0">Aucun backup disponible. Créez votre premier backup maintenant.</p>
        @endif
    </div>
</div>
@endsection

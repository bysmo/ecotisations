@extends('layouts.app')

@section('title', 'Détails de la Campagne')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope-paper"></i> Campagne : {{ $campagne->nom }}</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Nom</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->nom }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Sujet</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->sujet }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($campagne->statut === 'brouillon')
                            <span class="badge bg-secondary">Brouillon</span>
                        @elseif($campagne->statut === 'en_cours')
                            <span class="badge bg-info">En cours</span>
                        @elseif($campagne->statut === 'terminee')
                            <span class="badge bg-success">Terminée</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Destinataires</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        Total : {{ $campagne->total_destinataires }} | 
                        Envoyés : <span class="badge bg-success">{{ $campagne->envoyes }}</span> | 
                        Échecs : <span class="badge bg-danger">{{ $campagne->echecs }}</span>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Créée par</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->creePar->name ?? '-' }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Message</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <div style="white-space: pre-wrap; background: #f8f9fa; padding: 0.5rem; border-radius: 0.25rem;">{{ $campagne->message }}</div>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-envelope"></i> Historique des envois ({{ $logs->total() }})
            </div>
            <div class="card-body">
                @if($logs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Destinataire</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Erreur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->membre->nom_complet ?? '-' }}</td>
                                        <td>{{ $log->destinataire_email }}</td>
                                        <td>
                                            @if($log->statut === 'envoye')
                                                <span class="badge bg-success">Envoyé</span>
                                            @elseif($log->statut === 'echec')
                                                <span class="badge bg-danger">Échec</span>
                                            @else
                                                <span class="badge bg-warning">En attente</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->envoye_at ? $log->envoye_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $log->erreur ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center">Aucun log disponible</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('campagnes.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>
@endsection

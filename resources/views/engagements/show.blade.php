@extends('layouts.app')

@section('title', 'Détails de l\'Engagement')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-clipboard-check"></i> Engagement {{ $engagement->numero }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Numéro</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <strong>{{ $engagement->numero ?? '-' }}</strong>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Membre</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $engagement->membre->nom_complet ?? '-' }} ({{ $engagement->membre->numero ?? '-' }})
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Cotisation</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $engagement->cotisation->nom ?? '-' }}</dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Caisse</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $engagement->cotisation->caisse->nom ?? '-' }}</dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Périodicité</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-info">{{ ucfirst($engagement->periodicite ?? 'mensuelle') }}</span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Montant engagé</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-primary">
                            {{ number_format($engagement->montant_engage, 0, ',', ' ') }} XOF
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Montant payé</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-success">
                            {{ number_format($montantPaye, 0, ',', ' ') }} XOF
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Reste à payer</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge {{ $resteAPayer > 0 ? 'bg-warning' : 'bg-success' }}">
                            {{ number_format($resteAPayer, 0, ',', ' ') }} XOF
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Période</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $engagement->periode_debut->format('d/m/Y') }} - {{ $engagement->periode_fin->format('d/m/Y') }}
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($engagement->statut === 'en_cours')
                            <span class="badge bg-info">En cours</span>
                        @elseif($engagement->statut === 'termine')
                            <span class="badge bg-success">Terminé</span>
                        @else
                            <span class="badge bg-secondary">Annulé</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Notes</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $engagement->notes ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-tools"></i> Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('engagements.pdf', $engagement) }}?v={{ time() }}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-file-pdf"></i> Voir l'aperçu PDF
                    </a>
                    <a href="{{ route('engagements.edit', $engagement) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="{{ route('engagements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

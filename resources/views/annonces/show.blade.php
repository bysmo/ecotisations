@extends('layouts.app')

@section('title', 'Détails de l\'Annonce')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-megaphone"></i> Détails de l'Annonce</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Titre</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <strong>{{ $annonce->titre }}</strong>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Contenu</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $annonce->contenu }}
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Type</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-{{ $annonce->type }}">
                            @if($annonce->type === 'info')
                                Info
                            @elseif($annonce->type === 'warning')
                                Avertissement
                            @elseif($annonce->type === 'success')
                                Succès
                            @else
                                Danger
                            @endif
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($annonce->isActive())
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Période</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($annonce->date_debut || $annonce->date_fin)
                            {{ $annonce->date_debut ? $annonce->date_debut->format('d/m/Y') : 'Début' }} - 
                            {{ $annonce->date_fin ? $annonce->date_fin->format('d/m/Y') : 'Fin' }}
                        @else
                            <span class="text-muted">Sans limite</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Ordre</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $annonce->ordre }}</dd>
                    
                    <dt class="col-sm-4" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Créée le</dt>
                    <dd class="col-sm-8" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $annonce->created_at ? $annonce->created_at->format('d/m/Y H:i') : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('annonces.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <div>
                <a href="{{ route('annonces.edit', $annonce) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

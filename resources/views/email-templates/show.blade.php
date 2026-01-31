@extends('layouts.app')

@section('title', 'Détails Template')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Détails Template</h1>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Nom</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <strong>{{ $emailTemplate->nom }}</strong>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Type</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <span class="badge bg-info">
                            @if($emailTemplate->type === 'paiement')
                                Paiement
                            @elseif($emailTemplate->type === 'engagement')
                                Engagement
                            @elseif($emailTemplate->type === 'authentification')
                                Authentification
                            @else
                                Autre
                            @endif
                        </span>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Sujet</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $emailTemplate->sujet }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Corps</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <pre style="white-space: pre-wrap; font-family: 'Ubuntu', sans-serif; font-weight: 300; background: #f8f9fa; padding: 1rem; border-radius: 4px;">{{ $emailTemplate->corps }}</pre>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($emailTemplate->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <div>
                <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

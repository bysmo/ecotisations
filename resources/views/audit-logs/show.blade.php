@extends('layouts.app')

@section('title', 'Détails du Log')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-journal-text"></i> Détails du Log #{{ $auditLog->id }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">ID</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->id }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Date/Heure</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Utilisateur</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->user->name ?? 'Système' }} ({{ $auditLog->user->email ?? '-' }})</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Action</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><span class="badge bg-info">{{ $auditLog->action }}</span></dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Modèle</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;"><code>{{ $auditLog->model }}</code></dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">ID Modèle</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->model_id ?? '-' }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Description</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->description ?? '-' }}</dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">IP</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $auditLog->ip_address ?? '-' }}</dd>
                </dl>
            </div>
        </div>
        
        @if($auditLog->old_values)
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-arrow-left"></i> Anciennes valeurs
                </div>
                <div class="card-body">
                    <pre style="font-size: 0.75rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        @endif
        
        @if($auditLog->new_values)
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-arrow-right"></i> Nouvelles valeurs
                </div>
                <div class="card-body">
                    <pre style="font-size: 0.75rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        @endif
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>
@endsection

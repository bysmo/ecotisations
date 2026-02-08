@extends('layouts.app')

@section('title', 'Configurer ' . $smsGateway->name)

@section('content')
<div class="page-header">
    <h1><i class="bi bi-gear"></i> Configurer : {{ $smsGateway->name }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-key"></i> Paramètres</div>
            <div class="card-body">
                @if(count($configFields) === 0)
                    <p class="text-muted mb-0">Cette passerelle ne nécessite pas de configuration (ex. Log).</p>
                    <a href="{{ route('sms-gateways.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Retour</a>
                @else
                    <form action="{{ route('sms-gateways.update', $smsGateway) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @foreach($configFields as $field)
                            <div class="mb-3">
                                <label for="config_{{ $field['key'] }}" class="form-label">{{ $field['label'] }} @if(!empty($field['required']))<span class="text-danger">*</span>@endif</label>
                                @if(($field['type'] ?? 'text') === 'password')
                                    <input type="password" 
                                           class="form-control @error('config.' . $field['key']) is-invalid @enderror" 
                                           id="config_{{ $field['key'] }}" 
                                           name="config[{{ $field['key'] }}]" 
                                           value="{{ old('config.' . $field['key'], $smsGateway->getConfig($field['key'])) }}"
                                           placeholder="Laisser vide pour ne pas modifier">
                                @else
                                    <input type="text" 
                                           class="form-control @error('config.' . $field['key']) is-invalid @enderror" 
                                           id="config_{{ $field['key'] }}" 
                                           name="config[{{ $field['key'] }}]" 
                                           value="{{ old('config.' . $field['key'], $smsGateway->getConfig($field['key'])) }}">
                                @endif
                                @error('config.' . $field['key'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sms-gateways.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Enregistrer</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle"></i> À propos</div>
            <div class="card-body">
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    {{ $smsGateway->description }}
                </p>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; color: #666;">
                    La passerelle active (définie dans la liste) sera utilisée pour envoyer les codes OTP aux membres lors de leur inscription.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Passerelles SMS')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-chat-dots"></i> Passerelles SMS</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des passerelles</span>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">La passerelle active est utilisée pour l'envoi des codes OTP lors de l'inscription des membres. Une seule passerelle peut être active à la fois.</p>
        @if($gateways->count() > 0)
            <style>
                .table-sms-gateways { margin-bottom: 0; }
                .table-sms-gateways thead th { padding: 0.12rem 0.25rem !important; font-size: 0.6rem !important; line-height: 1.05 !important; font-weight: 300 !important; font-family: 'Ubuntu', sans-serif !important; color: #fff !important; background-color: var(--primary-dark-blue) !important; vertical-align: middle !important; }
                .table-sms-gateways tbody td { padding: 0.12rem 0.25rem !important; font-size: 0.6rem !important; line-height: 1.05 !important; font-weight: 300 !important; font-family: 'Ubuntu', sans-serif !important; color: var(--primary-dark-blue) !important; vertical-align: middle !important; border-bottom: 1px solid #f0f0f0 !important; }
                .table-sms-gateways tbody tr:last-child td { border-bottom: none !important; }
                .table-sms-gateways .badge { font-size: 0.55rem !important; font-weight: 300 !important; }
                .table-sms-gateways .actions-cell .btn,
                .table-sms-gateways .actions-cell .btn-group .btn { padding: 0.08rem 0.2rem !important; font-size: 0.5rem !important; min-height: 16px !important; width: 22px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; }
                .table-sms-gateways .actions-cell .btn i { font-size: 0.6rem !important; }
                .table-sms-gateways .actions-cell form { display: inline; }
            </style>
            <div class="table-responsive">
                <table class="table table-sms-gateways table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Passerelle</th>
                            <th>Description</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gateways as $gateway)
                            <tr>
                                <td><strong>{{ $gateway->name }}</strong></td>
                                <td><span class="text-muted" style="font-size: 0.58rem;">{{ Str::limit($gateway->description ?? '-', 60) }}</span></td>
                                <td class="text-center">
                                    @if($gateway->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="actions-cell btn-group btn-group-sm">
                                        @if($gateway->code !== 'log' && count(\App\Models\SmsGateway::configFields($gateway->code)) > 0)
                                            <a href="{{ route('sms-gateways.edit', $gateway) }}" class="btn btn-outline-primary" title="Configurer"><i class="bi bi-gear"></i></a>
                                        @endif
                                        @if(!$gateway->is_active)
                                            <form action="{{ route('sms-gateways.toggle', $gateway) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-success" title="Activer"><i class="bi bi-toggle-off"></i></button>
                                            </form>
                                        @else
                                            <span class="btn btn-outline-secondary disabled" title="Passerelle active"><i class="bi bi-toggle-on"></i></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-chat-dots" style="font-size: 2rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0">Aucune passerelle. Rechargez la page pour initialiser les passerelles par défaut.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.membre')

@section('title', 'Mes notifications')

@section('content')
<div class="page-header">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-bell"></i> Mes notifications
    </h1>
</div>

<div class="card">
    <div class="card-header" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-list-ul"></i> Toutes les notifications
    </div>
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th style="width: 100px;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr class="{{ $notification->read_at ? '' : 'table-primary' }}">
                                <td>
                                    @if(!$notification->read_at)
                                        <i class="bi bi-circle-fill text-primary" style="font-size: 0.5rem;"></i>
                                    @endif
                                </td>
                                <td>{{ $notification->data['title'] ?? 'Notification' }}</td>
                                <td>{{ $notification->data['message'] ?? '' }}</td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($notification->read_at)
                                        <span class="badge bg-secondary">Lu</span>
                                    @else
                                        <span class="badge bg-primary">Non lu</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-bell-slash" style="font-size: 3rem; color: #adb5bd;"></i>
                <p class="mt-3 text-muted">Aucune notification</p>
                <a href="{{ route('membre.dashboard') }}" class="btn btn-primary btn-sm mt-2">
                    <i class="bi bi-house"></i> Retour au dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

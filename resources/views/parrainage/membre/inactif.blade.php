@extends('layouts.membre')

@section('title', 'Parrainage')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card border-0 shadow-sm p-5">
                <i class="bi bi-people display-1 text-muted mb-3"></i>
                <h4 class="fw-bold">Programme de parrainage</h4>
                <p class="text-muted">Le programme de parrainage n'est pas encore disponible. Revenez bientôt !</p>
                <a href="{{ route('membre.dashboard') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-arrow-left me-1"></i>Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

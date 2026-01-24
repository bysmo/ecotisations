@extends('layouts.app')

@section('title', 'Modifier un Tag')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier le Tag</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Informations du Tag
    </div>
    <div class="card-body">
        <form action="{{ route('tags.update', $tag) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nom" class="form-label">
                    Nom du tag <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('nom') is-invalid @enderror" 
                       id="nom" 
                       name="nom" 
                       value="{{ old('nom', $tag->nom) }}" 
                       required>
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="font-size: 0.7rem;">Le nom du tag doit être unique</small>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3">{{ old('description', $tag->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('tags.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

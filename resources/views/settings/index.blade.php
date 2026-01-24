@extends('layouts.app')

@section('title', 'Paramètres Généraux')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-sliders"></i> Paramètres Généraux</h1>
</div>

<!-- Statut du Scheduler -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header" style="background-color: var(--primary-dark-blue); color: white; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                <i class="bi bi-clock-history"></i> Statut du Scheduler Cron
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-2"><strong>Statut :</strong></span>
                            @if($schedulerStatus['configured'])
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Configuré
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Non configuré
                                </span>
                            @endif
                        </div>
                        
                        @if($schedulerStatus['last_run'])
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event"></i> 
                                    Dernière exécution : {{ $schedulerStatus['last_run'] }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0" style="font-size: 0.75rem; font-family: 'Ubuntu', sans-serif;">
                            <strong><i class="bi bi-info-circle"></i> Configuration cPanel :</strong><br>
                            <small>Pour configurer le scheduler sur votre serveur cPanel, ajoutez cette commande dans le Cron Jobs de cPanel :</small><br>
                            <code style="font-size: 0.7rem; background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 3px; display: block; margin-top: 0.5rem; word-break: break-all;">
                                * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                            </code>
                            <small class="d-block mt-2">
                                <strong>Étapes :</strong><br>
                                1. Connectez-vous à votre cPanel<br>
                                2. Allez dans "Cron Jobs" (Tâches planifiées)<br>
                                3. Sélectionnez "Toutes les minutes" dans "Common Settings"<br>
                                4. Collez la commande ci-dessus dans le champ "Command"<br>
                                5. Cliquez sur "Ajouter une nouvelle tâche Cron"
                            </small>
                        </div>
                    </div>
                </div>
                
                @if(!$schedulerStatus['configured'])
                    <div class="alert alert-warning mt-3" style="font-size: 0.75rem; font-family: 'Ubuntu', sans-serif;">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Important :</strong> Sans le scheduler configuré, les rappels automatiques (paiements en retard, alertes de caisses, engagements à échéance) ne fonctionneront pas automatiquement.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Section À propos -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-clock-history"></i> Scheduler Cron
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Le scheduler Laravel permet d'exécuter automatiquement des tâches planifiées telles que les rappels de paiements, les alertes de caisses et les notifications d'engagements.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-robot"></i> Tâches automatiques
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Rappels paiements :</strong> Envoyés quotidiennement à 9h</li>
                    <li><strong>Alertes caisses :</strong> Vérification des soldes faibles</li>
                    <li><strong>Notifications :</strong> Engagements arrivant à échéance</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Configuration requise
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Configurez le cron job dans cPanel pour activer toutes les fonctionnalités automatiques. Sans cette configuration, les notifications doivent être déclenchées manuellement.
                </p>
            </div>
        </div>
    </div>
</div>

@if($settings->count() === 0)
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        <strong>Aucun paramètre configuré.</strong><br>
        <small>Exécutez le seeder pour créer les paramètres par défaut :</small><br>
        <code>php artisan db:seed --class=AppSettingSeeder</code>
    </div>
@endif

@if($settings->count() > 0)
<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    @foreach($settingsByGroup as $groupe => $settings)
            @if($groupe === 'entreprise')
            @php
                $entrepriseSettings = $settings->filter(function($s) {
                    return $s->cle !== 'entreprise_a_propos';
                });
            @endphp
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Section Entreprise -->
                    <div class="card mb-3">
                        <div class="card-header" style="background-color: var(--primary-dark-blue); color: white; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            <i class="bi bi-building"></i> Informations de l'entreprise
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($entrepriseSettings as $setting)
                                    @if($setting->cle === 'entreprise_logo')
                                        <!-- Logo sur toute la largeur -->
                                        <div class="col-12 mb-3">
                                            <label for="setting_{{ $setting->cle }}" class="form-label">
                                                {{ $setting->description ?? 'Logo' }}
                                            </label>
                                            <div class="d-flex align-items-center gap-3">
                                                @php
                                                    $logoPath = $setting->valeur;
                                                    $logoFullPath = $logoPath ? storage_path('app/public/' . $logoPath) : null;
                                                    $logoExists = $logoFullPath && \Illuminate\Support\Facades\File::exists($logoFullPath);
                                                    // Si le lien symbolique existe, utiliser asset, sinon utiliser la route
                                                    $publicStorageExists = \Illuminate\Support\Facades\File::exists(public_path('storage'));
                                                    if ($logoExists) {
                                                        if ($publicStorageExists) {
                                                            $logoUrl = asset('storage/' . $logoPath);
                                                        } else {
                                                            // Extraire le nom du fichier depuis logos/filename.ext
                                                            $filename = basename($logoPath);
                                                            $logoUrl = route('storage.logo', ['filename' => $filename]);
                                                        }
                                                    } else {
                                                        $logoUrl = null;
                                                    }
                                                @endphp
                                                @if($logoExists && $logoUrl)
                                                    <div>
                                                        <img src="{{ $logoUrl }}" 
                                                             alt="Logo" 
                                                             style="max-width: 150px; max-height: 80px; object-fit: contain; border: 1px solid #ddd; padding: 5px; background: #f8f9fa;">
                                                        <small class="d-block text-muted mt-1">Logo actuel</small>
                                                    </div>
                                                @else
                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                        @if($setting->valeur)
                                                            Logo configuré mais fichier introuvable<br>
                                                            <small>Chemin: {{ $setting->valeur }}</small>
                                                        @else
                                                            Aucun logo téléchargé
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <input type="file" 
                                                           class="form-control form-control-sm" 
                                                           id="entreprise_logo_upload" 
                                                           name="entreprise_logo_upload" 
                                                           accept="image/*">
                                                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Autres champs en 2 colonnes -->
                                        <div class="col-md-6 mb-3">
                                            <label for="setting_{{ $setting->cle }}" class="form-label">
                                                {{ $setting->description ?? $setting->cle }}
                                            </label>
                                            <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}" 
                                                   class="form-control" 
                                                   id="setting_{{ $setting->cle }}" 
                                                   name="{{ $setting->cle }}" 
                                                   value="{{ $setting->valeur }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Section À propos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="bi bi-info-circle"></i> À propos
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                <i class="bi bi-building"></i> Informations de l'entreprise
                            </h6>
                            <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                Ces informations seront utilisées dans les récapitulatifs PDF envoyés aux membres lors du traitement de fin de mois. Assurez-vous que toutes les informations sont complètes et à jour.
                            </p>
                            
                            <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                <i class="bi bi-file-pdf"></i> Utilisation dans les PDF
                            </h6>
                            <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                Le logo, le nom, l'adresse, le contact et l'email de l'entreprise apparaîtront automatiquement dans l'en-tête de chaque récapitulatif PDF généré.
                            </p>
                            
                            <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                <i class="bi bi-lightbulb"></i> Conseils
                            </h6>
                            <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                                <li><strong>Logo :</strong> Format JPG, PNG ou GIF (max 2MB)</li>
                                <li><strong>Champs requis :</strong> Remplissez au minimum le nom de l'entreprise</li>
                                <li><strong>Mise à jour :</strong> Les modifications prennent effet immédiatement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Autres groupes de paramètres (general, notifications, backup, affichage) -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header" style="background-color: var(--primary-dark-blue); color: white; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            <i class="bi bi-folder"></i> {{ ucfirst($groupe) }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($settings as $setting)
                                    @if($setting->type === 'boolean')
                                        <!-- Checkbox sur toute la largeur -->
                                        <div class="col-12 mb-3">
                                            <label for="setting_{{ $setting->cle }}" class="form-label">
                                                {{ $setting->description ?? $setting->cle }}
                                            </label>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="setting_{{ $setting->cle }}" 
                                                       name="{{ $setting->cle }}" 
                                                       value="1"
                                                       {{ filter_var($setting->valeur, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="setting_{{ $setting->cle }}">
                                                    Activé
                                                </label>
                                            </div>
                                        </div>
                                    @elseif($setting->type === 'json')
                                        <!-- Textarea sur toute la largeur -->
                                        <div class="col-12 mb-3">
                                            <label for="setting_{{ $setting->cle }}" class="form-label">
                                                {{ $setting->description ?? $setting->cle }}
                                            </label>
                                            <textarea class="form-control" 
                                                      id="setting_{{ $setting->cle }}" 
                                                      name="{{ $setting->cle }}" 
                                                      rows="3">{{ is_string($setting->valeur) ? $setting->valeur : json_encode($setting->valeur, JSON_PRETTY_PRINT) }}</textarea>
                                        </div>
                                    @else
                                        <!-- Autres champs en 2 colonnes -->
                                        <div class="col-md-6 mb-3">
                                            <label for="setting_{{ $setting->cle }}" class="form-label">
                                                {{ $setting->description ?? $setting->cle }}
                                            </label>
                                            <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}" 
                                                   class="form-control" 
                                                   id="setting_{{ $setting->cle }}" 
                                                   name="{{ $setting->cle }}" 
                                                   value="{{ $setting->valeur }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Section À propos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="bi bi-info-circle"></i> À propos
                        </div>
                        <div class="card-body">
                            @if($groupe === 'general')
                                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-gear"></i> Paramètres généraux
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Ces paramètres définissent les informations de base de l'application, incluant le nom, la description, l'email de contact et la devise utilisée.
                                </p>
                                
                                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-lightbulb"></i> Configuration
                                </h6>
                                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                                    <li><strong>Nom :</strong> Apparaît dans l'interface</li>
                                    <li><strong>Email :</strong> Utilisé pour les notifications</li>
                                    <li><strong>Devise :</strong> XOF (Franc CFA) par défaut</li>
                                </ul>
                            @elseif($groupe === 'notifications')
                                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-bell"></i> Notifications
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Configurez les notifications automatiques pour être alerté des paiements en retard, des soldes de caisses faibles et des engagements arrivant à échéance.
                                </p>
                                
                                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-exclamation-triangle"></i> Seuil d'alerte
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Définissez le montant minimum en dessous duquel une alerte sera envoyée pour chaque caisse. Les notifications sont envoyées quotidiennement si le solde est inférieur au seuil.
                                </p>
                                
                                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-calendar-check"></i> Rappels de paiement
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Configurez le nombre de jours avant l'échéance pour envoyer un rappel aux membres concernant leurs cotisations récurrentes. Par défaut, les rappels sont envoyés 3 jours avant l'échéance.
                                </p>
                            @elseif($groupe === 'backup')
                                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-database"></i> Sauvegarde
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Configurez la fréquence recommandée pour les sauvegardes et la durée de conservation des fichiers de backup.
                                </p>
                                
                                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-shield-check"></i> Bonnes pratiques
                                </h6>
                                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                                    <li><strong>Fréquence :</strong> Quotidienne recommandée</li>
                                    <li><strong>Conservation :</strong> 30 jours minimum</li>
                                    <li><strong>Sécurité :</strong> Stockez les backups hors site</li>
                                </ul>
                            @elseif($groupe === 'affichage')
                                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-display"></i> Affichage
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Personnalisez l'affichage de l'application, notamment le nombre d'éléments par page dans les listes et l'affichage des statistiques sur le tableau de bord.
                                </p>
                                
                                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-sliders"></i> Pagination
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Le nombre d'éléments par page détermine combien d'items sont affichés dans les listes (membres, paiements, cotisations, etc.). Par défaut, 15 éléments sont affichés par page.
                                </p>
                            @else
                                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                                    <i class="bi bi-info-circle"></i> Paramètres {{ ucfirst($groupe) }}
                                </h6>
                                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                                    Configurez les paramètres de cette section selon vos besoins. Les modifications prennent effet immédiatement après sauvegarde.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    
    <div class="d-flex justify-content-between">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Enregistrer les paramètres
        </button>
    </div>
</form>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-speedometer2"></i> Tableau de bord</h1>
</div>

<!-- Filtres de période -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2">
            <div class="col-md-4">
                <label for="date_debut" class="form-label" style="font-weight: 300; font-size: 0.75rem;">Date début</label>
                <input type="date" 
                       name="date_debut" 
                       id="date_debut"
                       class="form-control form-control-sm" 
                       value="{{ $dateDebut }}">
            </div>
            <div class="col-md-4">
                <label for="date_fin" class="form-label" style="font-weight: 300; font-size: 0.75rem;">Date fin</label>
                <input type="date" 
                       name="date_fin" 
                       id="date_fin"
                       class="form-control form-control-sm" 
                       value="{{ $dateFin }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filtrer
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Indicateurs principaux -->
<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: var(--primary-dark-blue);">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Membres actifs</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $totalMembres }}</h5>
                    </div>
                    <i class="bi bi-people" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: var(--primary-blue);">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Caisses actives</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $totalCaisses }}</h5>
                    </div>
                    <i class="bi bi-cash-coin" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #28a745;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Revenus période</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($revenusPeriode, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-wallet2" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #17a2b8;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Solde total caisses</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($soldeTotalCaisses, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-bank" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #6c757d;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Total cotisations</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($totalCotisationsMontant, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-file-earmark-text" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #ffc107;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Engagements</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($totalEngagements, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-clipboard-check" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #20c997;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Payé sur engagements</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($totalPayeEngagements, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #dc3545;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Reste engagements</h6>
                        <h5 class="mb-0" style="font-size: 0.9rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                            {{ number_format($totalEngagements - $totalPayeEngagements, 0, ',', ' ') }} XOF
                        </h5>
                    </div>
                    <i class="bi bi-exclamation-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Évolution des Paiements
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($evolutionPaiements->count() > 0)
                    <canvas id="evolutionChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Répartition par Mode de Paiement
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($paiementsParMode->count() > 0)
                    <canvas id="modePaiementChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par caisse -->
<div class="row mb-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin"></i> Statistiques par Caisse
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($statistiquesCaisses->count() > 0)
                    <canvas id="caissesChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune caisse active</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Détails par Caisse
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($statistiquesCaisses->count() > 0)
                    <canvas id="detailsCaissesChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune caisse active</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par cotisation -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-text"></i> Top 10 Cotisations
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($statistiquesCotisations->count() > 0)
                    <canvas id="topCotisationsChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune cotisation active</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people"></i> Top 10 Membres
            </div>
            <div class="card-body" style="height: 350px; position: relative;">
                @if($topMembres->count() > 0)
                    <canvas id="topMembresChart"></canvas>
                @else
                    <div class="text-center py-3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun paiement sur la période</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par membre -->
@if(isset($statistiquesMembres) && $statistiquesMembres->count() > 0)
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people-fill"></i> Statistiques par Membre
            </div>
            <div class="card-body">
                <style>
                    .table-stat-membres thead th {
                        padding: 0.25rem 0.5rem !important;
                        font-size: 0.65rem !important;
                        line-height: 1.2 !important;
                        vertical-align: middle !important;
                        font-weight: 300 !important;
                        font-family: 'Ubuntu', sans-serif !important;
                        color: var(--primary-dark-blue) !important;
                    }
                    
                    .table-stat-membres tbody td {
                        padding: 0.25rem 0.5rem !important;
                        font-size: 0.7rem !important;
                        line-height: 1.2 !important;
                        vertical-align: middle !important;
                        font-weight: 300 !important;
                        font-family: 'Ubuntu', sans-serif !important;
                        color: var(--primary-dark-blue) !important;
                    }
                    
                    .table-stat-membres tbody tr:nth-child(even) {
                        background-color: rgba(30, 58, 95, 0.08) !important;
                    }
                    
                    .table-stat-membres tbody tr:hover {
                        background-color: rgba(30, 58, 95, 0.15) !important;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-stat-membres">
                        <thead>
                            <tr>
                                <th>Membre</th>
                                <th class="text-end">Nombre paiements</th>
                                <th class="text-end">Total payé (XOF)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistiquesMembres as $statMembre)
                                <tr>
                                    <td>{{ $statMembre['nom'] }}</td>
                                    <td class="text-end">{{ $statMembre['nombre_paiements'] }}</td>
                                    <td class="text-end">{{ number_format($statMembre['total_paye'], 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.addEventListener('load', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js non chargé');
        return;
    }

    // Configuration commune
    Chart.defaults.font.family = 'Ubuntu';
    Chart.defaults.font.weight = '300';
    Chart.defaults.font.style = 'normal';

    // Données depuis le serveur
    const evolutionData = @json($evolutionPaiements->values());
    const modeData = @json($paiementsParMode->values());
    const caissesData = @json($statistiquesCaisses->values());
    const cotisationsData = @json($statistiquesCotisations->values());
    const membresData = @json($topMembres->values());

    // Graphique Évolution des Paiements
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx && evolutionData && evolutionData.length > 0) {
        const labels = evolutionData.map(item => {
            const d = new Date(item.date);
            return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        });
        const values = evolutionData.map(item => parseFloat(item.total || 0));
        
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Montant (XOF)',
                    data: values,
                    borderColor: 'rgb(30, 58, 95)',
                    backgroundColor: 'rgba(30, 58, 95, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XOF';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Répartition par Mode de Paiement
    const modeCtx = document.getElementById('modePaiementChart');
    if (modeCtx && modeData && modeData.length > 0) {
        const labels = modeData.map(item => {
            const m = item.mode_paiement || '';
            return m.charAt(0).toUpperCase() + m.slice(1).replace('_', ' ');
        });
        const values = modeData.map(item => parseFloat(item.total || 0));
        
        new Chart(modeCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Montant (XOF)',
                    data: values,
                    backgroundColor: 'rgba(30, 58, 95, 0.8)',
                    borderColor: 'rgb(30, 58, 95)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { style: 'normal', size: 10, weight: '300', family: 'Ubuntu' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XOF';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            font: { style: 'normal', size: 10, weight: '300', family: 'Ubuntu' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { style: 'normal', size: 10, weight: '300', family: 'Ubuntu' },
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Statistiques par Caisse
    const caissesCtx = document.getElementById('caissesChart');
    if (caissesCtx && caissesData && caissesData.length > 0) {
        const labels = caissesData.map(item => item.nom || '');
        const entrees = caissesData.map(item => parseFloat(item.entrees || 0));
        const sorties = caissesData.map(item => parseFloat(item.sorties || 0));
        const net = caissesData.map(item => parseFloat(item.net || 0));
        
        new Chart(caissesCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Entrées',
                        data: entrees,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgb(40, 167, 69)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sorties',
                        data: sorties,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgb(220, 53, 69)',
                        borderWidth: 1
                    },
                    {
                        label: 'Net',
                        data: net,
                        backgroundColor: 'rgba(30, 58, 95, 0.8)',
                        borderColor: 'rgb(30, 58, 95)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XOF';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Détails par Caisse
    const detailsCtx = document.getElementById('detailsCaissesChart');
    if (detailsCtx && caissesData && caissesData.length > 0) {
        const labels = caissesData.map(item => item.nom || '');
        const soldes = caissesData.map(item => parseFloat(item.solde_actuel || 0));
        
        new Chart(detailsCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Solde actuel (XOF)',
                    data: soldes,
                    backgroundColor: 'rgba(30, 58, 95, 0.8)',
                    borderColor: 'rgb(30, 58, 95)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XOF';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Top 10 Cotisations
    const cotisationsCtx = document.getElementById('topCotisationsChart');
    if (cotisationsCtx && cotisationsData && cotisationsData.length > 0) {
        const labels = cotisationsData.map(item => {
            const nom = item.nom || '';
            return nom.length > 20 ? nom.substring(0, 20) + '...' : nom;
        });
        const values = cotisationsData.map(item => parseFloat(item.montant_total || 0));
        
        if (values.some(v => v > 0)) {
            new Chart(cotisationsCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Montant total (XOF)',
                        data: values,
                        backgroundColor: 'rgba(30, 58, 95, 0.8)',
                        borderColor: 'rgb(30, 58, 95)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('fr-FR').format(context.parsed.x) + ' XOF';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique Top 10 Membres
    const membresCtx = document.getElementById('topMembresChart');
    if (membresCtx && membresData && membresData.length > 0) {
        const labels = membresData.map(item => {
            let nom = '';
            if (item.nom_complet) {
                nom = item.nom_complet;
            } else if (item.nom || item.prenom) {
                nom = (item.nom || '') + ' ' + (item.prenom || '');
            }
            return (nom.trim().length > 20 ? nom.trim().substring(0, 20) + '...' : nom.trim()) || 'Inconnu';
        });
        const values = membresData.map(item => parseFloat(item.total_paye || item.paiements_sum_montant || 0));
        
        if (values.some(v => v > 0)) {
            new Chart(membresCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total payé (XOF)',
                        data: values,
                        backgroundColor: 'rgba(30, 58, 95, 0.8)',
                        borderColor: 'rgb(30, 58, 95)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('fr-FR').format(context.parsed.x) + ' XOF';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' XOF';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush

@endsection

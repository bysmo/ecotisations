<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planifier les vérifications de rappels et notifications
Schedule::command('app:check-overdue-payments')
    ->dailyAt('09:00')
    ->description('Vérifier les paiements en retard');

Schedule::command('app:check-low-balances')
    ->dailyAt('09:00')
    ->description('Vérifier les caisses avec solde faible');

Schedule::command('app:check-upcoming-engagements')
    ->dailyAt('09:00')
    ->description('Vérifier les engagements arrivant à échéance');

Schedule::command('app:check-upcoming-payments')
    ->dailyAt('09:00')
    ->description('Vérifier les paiements de cotisations à venir et envoyer des rappels');

// Audit financier : racine Merkle toutes les heures
Schedule::command('audit:merkle --period=1')
    ->hourly()
    ->description('Calcul racine Merkle journal audit');

// Réconciliation soldes (calculé vs livre) toutes les 5 minutes
Schedule::command('audit:reconcile')
    ->everyFiveMinutes()
    ->description('Réconciliation soldes caisses');

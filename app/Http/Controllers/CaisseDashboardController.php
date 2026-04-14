<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\MouvementCaisse;
use App\Models\Membre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseDashboardController extends Controller
{
    /**
     * Dashboard des comptes
     */
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $last30Days = $now->copy()->subDays(30);

        // 1. Statistiques Globales (KPIs)
        $caisses = Caisse::all();
        
        // Liquidité (Courant + Epargne + Tontine avec solde positif)
        $totalLiquidite = $caisses->whereIn('type', ['courant', 'epargne', 'tontine'])->sum(fn($c) => max(0, $c->solde_actuel));
        
        // Encours de Crédit (Dettes)
        $totalCredit = $caisses->where('type', 'credit')->sum(fn($c) => (float) abs($c->solde_actuel));
        
        // Impayés (Retards)
        $totalImpayes = $caisses->where('type', 'impayes')->sum(fn($c) => (float) abs($c->solde_actuel));
        
        // Volume de transactions (30 derniers jours)
        $mouvements30j = MouvementCaisse::where('date_operation', '>=', $last30Days)->get();
        $volumeTransactions = $mouvements30j->sum(fn($m) => (float) $m->montant);

        // 2. Répartition par type de compte (pour le graphique camembert)
        $repartitionType = [
            'Courant' => $caisses->where('type', 'courant')->count(),
            'Épargne' => $caisses->where('type', 'epargne')->count(),
            'Tontine' => $caisses->where('type', 'tontine')->count(),
            'Crédit'  => $caisses->where('type', 'credit')->count(),
            'Impayés' => $caisses->where('type', 'impayes')->count(),
        ];

        // 3. Flux hebdomadaires (In vs Out)
        $fluxSemaine = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $mouvDate = MouvementCaisse::whereDate('date_operation', $date)->get();
            
            $fluxSemaine[] = [
                'date' => $now->copy()->subDays($i)->translatedFormat('D d M'),
                'entree' => $mouvDate->where('sens', 'entree')->sum(fn($m) => (float) $m->montant),
                'sortie' => $mouvDate->where('sens', 'sortie')->sum(fn($m) => (float) $m->montant),
            ];
        }

        // 4. Top 5 Clients par Solde (Liquidité cumulée)
        $topClients = Membre::with('comptes')
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'nom' => $m->nom_complet,
                    'solde' => $m->solde_global,
                ];
            })
            ->sortByDesc('solde')
            ->take(5);

        // 5. Derniers Mouvements
        $derniersMouvements = MouvementCaisse::with('caisse.membre')
            ->orderBy('date_operation', 'desc')
            ->limit(5)
            ->get();

        return view('caisses.dashboard', compact(
            'totalLiquidite',
            'totalCredit',
            'totalImpayes',
            'volumeTransactions',
            'repartitionType',
            'fluxSemaine',
            'topClients',
            'derniersMouvements'
        ));
    }
}

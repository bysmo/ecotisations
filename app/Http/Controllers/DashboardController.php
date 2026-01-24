<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Membre;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\Engagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     */
    public function index(Request $request)
    {
        // Période par défaut (30 derniers jours)
        $dateDebut = $request->input('date_debut', now()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->format('Y-m-d'));

        // Indicateurs généraux
        $totalMembres = Membre::where('statut', 'actif')->count();
        $totalCaisses = Caisse::where('statut', 'active')->count();
        $totalCotisations = Cotisation::where('actif', true)->count();
        // Total des montants fixes des cotisations (seulement celles avec montant fixe)
        $totalCotisationsMontant = Cotisation::where('actif', true)
            ->where('type_montant', 'fixe')
            ->whereNotNull('montant')
            ->sum('montant') ?? 0;
        
        // Total des paiements sur la période
        $totalPaiements = Paiement::whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');
        
        // Revenus par période (paiements sur la période)
        $revenusPeriode = $totalPaiements;
        
        // Total des engagements
        $totalEngagements = Engagement::where('statut', 'en_cours')->sum('montant_engage');
        
        // Total des montants payés sur les engagements
        $totalPayeEngagements = Engagement::where('statut', 'en_cours')
            ->get()
            ->sum(function($engagement) {
                return $engagement->montant_paye;
            });
        
        // Solde total des caisses
        $soldeTotalCaisses = Caisse::where('statut', 'active')
            ->get()
            ->sum('solde_actuel');

        // Statistiques par caisse
        $statistiquesCaisses = Caisse::where('statut', 'active')
            ->withCount(['paiements' => function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_paiement', [$dateDebut, $dateFin]);
            }])
            ->get()
            ->map(function($caisse) use ($dateDebut, $dateFin) {
                $entrees = DB::table('mouvements_caisse')
                    ->where('caisse_id', $caisse->id)
                    ->where('sens', 'entree')
                    ->whereBetween('date_operation', [$dateDebut, $dateFin])
                    ->sum('montant');
                
                $sorties = DB::table('mouvements_caisse')
                    ->where('caisse_id', $caisse->id)
                    ->where('sens', 'sortie')
                    ->whereBetween('date_operation', [$dateDebut, $dateFin])
                    ->sum('montant');
                
                return [
                    'nom' => $caisse->nom,
                    'solde_actuel' => $caisse->solde_actuel,
                    'entrees' => $entrees,
                    'sorties' => $sorties,
                    'net' => $entrees - $sorties,
                ];
            });

        // Statistiques par cotisation
        $statistiquesCotisations = Cotisation::where('actif', true)
            ->withCount(['paiements' => function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_paiement', [$dateDebut, $dateFin]);
            }])
            ->get()
            ->map(function($cotisation) use ($dateDebut, $dateFin) {
                $montantTotal = Paiement::where('cotisation_id', $cotisation->id)
                    ->whereBetween('date_paiement', [$dateDebut, $dateFin])
                    ->sum('montant');
                
                return [
                    'nom' => $cotisation->nom,
                    'nombre_paiements' => $cotisation->paiements_count,
                    'montant_total' => $montantTotal,
                ];
            })
            ->sortByDesc('montant_total')
            ->take(10);

        // Évolution des paiements (par jour sur la période)
        $evolutionPaiements = Paiement::select(
                DB::raw('DATE(date_paiement) as date'),
                DB::raw('SUM(montant) as total')
            )
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Répartition des paiements par mode
        $paiementsParMode = Paiement::select(
                'mode_paiement',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(montant) as total')
            )
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->groupBy('mode_paiement')
            ->get();

        // Top 10 membres par montant payé
        $topMembres = Membre::where('statut', 'actif')
            ->withSum(['paiements' => function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_paiement', [$dateDebut, $dateFin]);
            }], 'montant')
            ->having('paiements_sum_montant', '>', 0)
            ->orderByDesc('paiements_sum_montant')
            ->take(10)
            ->get()
            ->map(function($membre) {
                $membre->total_paye = $membre->paiements_sum_montant ?? 0;
                return $membre;
            });

        // Statistiques par membre (total payé par membre)
        $statistiquesMembres = Membre::where('statut', 'actif')
            ->withSum(['paiements' => function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_paiement', [$dateDebut, $dateFin]);
            }], 'montant')
            ->having('paiements_sum_montant', '>', 0)
            ->orderByDesc('paiements_sum_montant')
            ->take(10)
            ->get()
            ->map(function($membre) use ($dateDebut, $dateFin) {
                return [
                    'nom' => $membre->nom . ' ' . $membre->prenom,
                    'total_paye' => $membre->paiements_sum_montant ?? 0,
                    'nombre_paiements' => $membre->paiements()->whereBetween('date_paiement', [$dateDebut, $dateFin])->count(),
                ];
            });

        return view('dashboard', compact(
            'totalMembres',
            'totalCaisses',
            'totalCotisations',
            'totalCotisationsMontant',
            'totalPaiements',
            'revenusPeriode',
            'totalEngagements',
            'totalPayeEngagements',
            'soldeTotalCaisses',
            'statistiquesCaisses',
            'statistiquesCotisations',
            'statistiquesMembres',
            'evolutionPaiements',
            'paiementsParMode',
            'topMembres',
            'dateDebut',
            'dateFin'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NanoCredit;
use App\Models\NanoCreditPalier;
use App\Models\User;
use App\Models\Membre;
use App\Models\NanoCreditGarant;
use App\Notifications\NanoCreditDemandeNotification;
use App\Notifications\GarantSollicitationNotification;
use App\Notifications\GarantRefusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembreNanoCreditController extends Controller
{
    /**
     * Liste des types de nano crédit disponibles + lien vers Mes nano crédits
     */
    public function index()
    {
        $membre = Auth::guard('membre')->user();

        if (!$membre->hasKycValide()) {
            return redirect()->route('membre.kyc.index')
                ->with('info', 'Vous devez soumettre votre dossier KYC et qu\'il soit validé avant de pouvoir faire une demande de nano crédit.');
        }

        $palier = $membre->nanoCreditPalier;
        
        // Si le membre n'a pas de palier (ne devrait pas arriver si KYC validé), on lui assigne le 1
        if (!$palier) {
            app(\App\Services\NanoCreditPalierService::class)->assignerPalierInitial($membre);
            $membre->refresh();
            $palier = $membre->nanoCreditPalier;
        }

        return view('membres.nano-credits.index', compact('membre', 'palier'));
    }

    /**
     * Mes nano crédits (demandes et crédits octroyés)
     */
    public function mes()
    {
        $membre = Auth::guard('membre')->user();
        $nanoCredits = $membre->nanoCredits()
            ->with('palier')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('membres.nano-credits.mes', compact('membre', 'nanoCredits'));
    }

    /**
     * Formulaire de demande (souscription) pour un type donné — le membre ne choisit que le montant.
     * Le contact et le canal sont récupérés du profil du membre lors de l'octroi par l'admin.
     */
    public function demander()
    {
        $membre = Auth::guard('membre')->user();

        if (!$membre->hasKycValide()) {
            return redirect()->route('membre.nano-credits')->with('error', 'KYC requis.');
        }

        $palier = $membre->nanoCreditPalier;
        if (!$palier) {
            return redirect()->route('membre.nano-credits')->with('error', 'Aucun palier de crédit assigné.');
        }

        if ($membre->hasCreditEnCours()) {
            return redirect()->route('membre.nano-credits')->with('error', 'Vous avez déjà un crédit en cours non soldé. Veuillez le rembourser avant d\'en prendre un nouveau.');
        }

        return view('membres.nano-credits.demander', compact('membre', 'palier'));
    }

    /**
     * Enregistrer la demande de nano crédit
     */
    public function storeDemande(Request $request)
    {
        $membre = Auth::guard('membre')->user();

        if (!$membre->hasKycValide()) {
            return redirect()->route('membre.nano-credits')->with('error', 'KYC requis.');
        }

        $palier = $membre->nanoCreditPalier;
        if (!$palier) {
            return redirect()->route('membre.nano-credits')->with('error', 'Aucun palier assigné.');
        }

        if ($membre->hasCreditEnCours()) {
            return redirect()->route('membre.nano-credits')->with('error', 'Vous avez déjà un crédit en cours non soldé. Veuillez le rembourser avant d\'en prendre un nouveau.');
        }

        $montantMax = (float) $palier->montant_plafond;

        $validated = $request->validate([
            'montant' => 'required|numeric|min:1000|max:' . $montantMax,
            'garant_ids' => 'required|array|size:' . $palier->nombre_garants,
            'garant_ids.*' => 'required|exists:membres,id',
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.min' => 'Le montant minimum est 1 000 XOF.',
            'montant.max' => 'Le montant maximum pour votre palier actuel est ' . number_format($montantMax, 0, ',', ' ') . ' XOF.',
            'garant_ids.required' => 'Vous devez sélectionner vos garants.',
            'garant_ids.size' => 'Vous devez sélectionner exactement ' . $palier->nombre_garants . ' garant(s).',
        ]);

        DB::beginTransaction();
        try {
            $nanoCredit = NanoCredit::create([
                'palier_id' => $palier->id,
                'membre_id' => $membre->id,
                'montant' => (int) round((float) $validated['montant'], 0),
                'statut' => 'demande_en_attente',
            ]);

            // Créer les sollicitations des garants
            foreach ($validated['garant_ids'] as $garantId) {
                $garantMembre = Membre::findOrFail($garantId);
                
                // Vérification supplémentaire de l'éligibilité
                // On crée une instance temporaire pour la validation
                $tempGarant = new NanoCreditGarant(['membre_id' => $garantId]);
                if (!NanoCreditGarant::membreEstEligibleGarant($garantMembre, $nanoCredit)) {
                     throw new \Exception("Le membre {$garantMembre->nom_complet} n'est pas éligible comme garant.");
                }

                $garantRecord = NanoCreditGarant::create([
                    'nano_credit_id' => $nanoCredit->id,
                    'membre_id' => $garantId,
                    'statut' => 'en_attente',
                ]);

                // Notifier le garant
                $garantMembre->notify(new GarantSollicitationNotification($garantRecord));
            }

            DB::commit();

            // Notification admin
            $admins = User::whereHas('roles', function ($q) {
                $q->where('slug', 'admin')->where('actif', true);
            })->get();
            foreach ($admins as $admin) {
                $admin->notify(new NanoCreditDemandeNotification($nanoCredit));
            }

            return redirect()->route('membre.nano-credits.mes')
                ->with('success', 'Votre demande a été enregistrée. Vos garants ont été notifiés pour validation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Recherche AJAX de garants éligibles
     */
    public function searchGuarantors(Request $request)
    {
        $search = $request->query('q');
        $membre = Auth::guard('membre')->user();
        $palier = $membre->nanoCreditPalier;

        if (!$palier) return response()->json([]);

        $query = Membre::where('id', '!=', $membre->id)
            ->where('statut', 'actif')
            ->whereHas('kycVerification', function($q) {
                 $q->where('statut', 'valide');
            })
            ->where('garant_qualite', '>=', $palier->min_garant_qualite ?? 0);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        $results = $query->limit(10)->get()->filter(function($m) {
            // Filtrage fin (limite de garanties actives)
            return !$m->aAtteintLimiteGaranties();
        })->map(function($m) {
            return [
                'id' => $m->id,
                'text' => $m->nom_complet . " (" . $m->telephone . ")",
                'qualite' => $m->garant_qualite,
            ];
        })->values();

        return response()->json($results);
    }

    /**
     * Formulaire pour modifier les garants (après un refus)
     */
    public function modifierGarants(NanoCredit $nanoCredit)
    {
        $membre = Auth::guard('membre')->user();
        if ($nanoCredit->membre_id !== $membre->id) abort(403);

        $palier = $nanoCredit->palier;
        $garantsRefuses = $nanoCredit->garants()->where('statut', 'refuse')->with('membre')->get();
        $garantsValides = $nanoCredit->garants()->whereIn('statut', ['accepte', 'en_attente'])->with('membre')->get();

        if ($garantsRefuses->isEmpty()) {
            return redirect()->route('membre.nano-credits.show', $nanoCredit)->with('info', 'Tous vos garants sont déjà en attente ou ont accepté.');
        }

        return view('membres.nano-credits.modifier-garants', compact('nanoCredit', 'palier', 'garantsRefuses', 'garantsValides'));
    }

    /**
     * Mettre à jour les garants refusés
     */
    public function updateGarants(Request $request, NanoCredit $nanoCredit)
    {
        $membre = Auth::guard('membre')->user();
        if ($nanoCredit->membre_id !== $membre->id) abort(403);

        $palier = $nanoCredit->palier;
        $nbRefuses = $nanoCredit->garants()->where('statut', 'refuse')->count();

        $validated = $request->validate([
            'new_garant_ids' => 'required|array|size:' . $nbRefuses,
            'new_garant_ids.*' => 'required|exists:membres,id',
        ]);

        DB::beginTransaction();
        try {
            // Supprimer les refusés
            $nanoCredit->garants()->where('statut', 'refuse')->delete();

            // Ajouter les nouveaux
            foreach ($validated['new_garant_ids'] as $garantId) {
                $garantMembre = Membre::findOrFail($garantId);
                
                $garantRecord = NanoCreditGarant::create([
                    'nano_credit_id' => $nanoCredit->id,
                    'membre_id' => $garantId,
                    'statut' => 'en_attente',
                ]);

                $garantMembre->notify(new GarantSollicitationNotification($garantRecord));
            }

            DB::commit();
            return redirect()->route('membre.nano-credits.mes')->with('success', 'Nouveaux garants sollicités avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Détail d'un nano crédit : tableau d'amortissement + historique des remboursements
     */
    public function show(NanoCredit $nanoCredit)
    {
        $membre = Auth::guard('membre')->user();

        if ($nanoCredit->membre_id !== $membre->id) {
            abort(403);
        }

        $nanoCredit->load(['palier', 'echeances', 'versements']);
        return view('membres.nano-credits.show', compact('membre', 'nanoCredit'));
    }

    private function normalizePhone(string $telephone): string
    {
        $digits = preg_replace('/\D/', '', $telephone);
        $indicatifs = ['221','223', '225', '226', '227', '228', '229'];
        foreach ($indicatifs as $code) {
            if (str_starts_with($digits, $code) && strlen($digits) > strlen($code)) {
                return substr($digits, strlen($code));
            }
        }
        return $digits;
    }
}

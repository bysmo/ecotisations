<?php

namespace App\Http\Controllers;

use App\Models\AuditChecksumLog;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    /**
     * Affiche l'historique des vérifications de l'intégrité de la base de données (Scans de Checksums).
     */
    public function index()
    {
        // On récupère les logs avec pagination (les plus récents en premier)
        $logs = AuditChecksumLog::orderBy('created_at', 'desc')->paginate(20);

        return view('audit-logs.security', compact('logs'));
    }

    /**
     * Lance manuellement un scan de sécurité sur demande de l'administrateur
     */
    public function scan()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('audit:checksums');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            // On peut logger la sortie si besoin, mais le résultat est de toute façon enregistré en base
            // par la commande elle-même.
            return back()->with('success', 'Le scan manuel des checksums a été exécuté avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du lancement du scan manuel : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le rapport détaillé d'un scan spécifique.
     */
    public function show($id)
    {
        $log = AuditChecksumLog::findOrFail($id);
        
        return view('audit-logs.security-show', compact('log'));
    }

    /**
     * Applique une action de remédiation sur une donnée corrompue
     */
    public function remediate(Request $request)
    {
        $request->validate([
            'model'  => 'required|string',
            'id'     => 'required|integer',
            'action' => 'required|in:restore,suspend,accept',
        ]);

        $modelClass = $request->input('model');
        $recordId   = $request->input('id');
        $action     = $request->input('action');

        if (!class_exists($modelClass)) {
            return back()->with('error', 'Le modèle spécifié est introuvable.');
        }

        $record = $modelClass::find($recordId);

        if (!$record) {
            return back()->with('error', "L'enregistrement ID $recordId est introuvable.");
        }

        try {
            switch ($action) {
                case 'restore':
                    // Recherche du dernier état valide dans l'audit_log
                    $lastLog = \App\Models\AuditLog::where('model', $modelClass)
                        ->where('model_id', $recordId)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if (!$lastLog || empty($lastLog->new_values)) {
                        return back()->with('error', 'Impossible de restaurer : aucune trace antérieure trouvée dans l\'AuditApplicatif.');
                    }

                    // Restaurer les champs
                    $ignoredKeys = ['id', 'checksum', 'created_at', 'updated_at', 'deleted_at'];
                    foreach ($lastLog->new_values as $key => $value) {
                         if (!in_array($key, $ignoredKeys) && array_key_exists($key, $record->getAttributes())) {
                             $record->{$key} = $value;
                         }
                    }
                    
                    // On sauvegarde silencieusement, Laravel déclenchera (saving) et recalculera le hash !
                    $record->save();
                    return back()->with('success', 'Restitution effectuée. L\'enregistrement a retrouvé ses valeurs précédentes saines et a été re-signé.');

                case 'accept':
                    // On force la sauvegarde telle quelle. Le hash (checksum) va être recalculé pour correspondre à l'état frauduleux/actuel.
                    // C'est l'équivalent d'un cache-clear forcé ou d'une amnistie
                    $record->save();
                    return back()->with('success', 'Altération acceptée. Le Checksum a été recalculé pour s\'aligner avec les données actuelles.');

                case 'suspend':
                    // Si c'est un membre, on le bloque. Si c'est lié à un membre, on bloque son compte
                    if ($modelClass === \App\Models\Membre::class) {
                        $record->statut = 'suspendu';
                        $record->save();
                        return back()->with('success', 'Membre proprement verrouillé (suspendu) suite à suspicion de corruption.');
                    } elseif (method_exists($record, 'membre') || filter_var($record->membre_id ?? null, FILTER_VALIDATE_INT)) {
                        $membreId = $record->membre_id ?? ($record->membre->id ?? null);
                        if ($membreId) {
                            $membre = \App\Models\Membre::find($membreId);
                            if ($membre) {
                                $membre->statut = 'suspendu';
                                $membre->save();
                                return back()->with('success', 'Le Membre rattaché à cette opération a été verrouillé (suspendu).');
                            }
                        }
                    }
                    return back()->with('error', 'L\'objet n\'a pas de compte Membre directement susceptible de suspension automatique.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la remédiation : ' . $e->getMessage());
        }
    }
}

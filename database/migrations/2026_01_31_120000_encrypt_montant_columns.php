<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Colonnes à chiffrer (table => [colonnes]).
     * Les valeurs existantes sont chiffrées avec APP_KEY.
     */
    protected array $columnsToEncrypt = [
        'caisses' => ['solde_initial'],
        'paiements' => ['montant'],
        'mouvements_caisse' => ['montant'],
        'cotisations' => ['montant'],
        'engagements' => ['montant_engage'],
        'remboursements' => ['montant'],
        'approvisionnements' => ['montant'],
        'transferts' => ['montant'],
        'sorties_caisse' => ['montant'],
        'fin_mois_logs' => ['montant_total'],
        'cotisation_versement_demandes' => ['montant_demande'],
        'nano_credits' => ['montant'],
        'nano_credit_types' => ['montant_min', 'montant_max', 'taux_interet'],
        'nano_credit_echeances' => ['montant'],
        'nano_credit_versements' => ['montant'],
        'epargne_plans' => ['montant_min', 'montant_max', 'taux_remuneration'],
        'epargne_souscriptions' => ['montant', 'solde_courant'],
        'epargne_echeances' => ['montant'],
        'epargne_versements' => ['montant'],
    ];

    public function up(): void
    {
        foreach ($this->columnsToEncrypt as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }
                Schema::table($table, function (Blueprint $blueprint) use ($column) {
                    $blueprint->longText($column)->nullable()->change();
                });
            }
        }

        $this->encryptExistingData();
    }

    protected function encryptExistingData(): void
    {
        foreach ($this->columnsToEncrypt as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $primaryKey = $this->getPrimaryKey($table);
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }
                DB::table($table)->orderBy($primaryKey)->chunk(100, function ($rows) use ($table, $column, $primaryKey) {
                    foreach ($rows as $row) {
                        $raw = $row->{$column};
                        if ($raw === null || $raw === '') {
                            continue;
                        }
                        try {
                            Crypt::decrypt($raw);
                            continue; // Déjà chiffré
                        } catch (\Throwable $e) {
                            // Pas chiffré, on chiffre
                        }
                        $value = is_numeric($raw) ? (float) $raw : $raw;
                        try {
                            $encrypted = Crypt::encrypt($value);
                            DB::table($table)->where($primaryKey, $row->{$primaryKey})->update([$column => $encrypted]);
                        } catch (\Throwable $e) {
                            // Ignorer en cas d'erreur
                        }
                    }
                });
            }
        }
    }

    protected function getPrimaryKey(string $table): string
    {
        return 'id';
    }

    public function down(): void
    {
        // Ne pas reconvertir en decimal : les valeurs sont chiffrées, un rollback propre nécessite
        // un script de déchiffrement ou une restauration de sauvegarde.
    }
};

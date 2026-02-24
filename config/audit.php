<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Clé secrète pour HMAC (audit_financier)
    |--------------------------------------------------------------------------
    | Utilisée pour signer chaque ligne du journal. Générer une clé forte :
    | php -r "echo bin2hex(random_bytes(32));"
    */
    'secret_key' => env('AUDIT_FINANCIER_SECRET', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Seuil d'écart pour alerte réconciliation (en unité monétaire)
    |--------------------------------------------------------------------------
    | Si |solde_calcule - solde_livre| > ce seuil → alerte critique.
    */
    'reconciliation_alert_threshold' => (float) env('AUDIT_RECONCILIATION_THRESHOLD', 0.01),

    /*
    |--------------------------------------------------------------------------
    | Geler les comptes en cas d'alerte
    |--------------------------------------------------------------------------
    | Si true, en cas d'écart > seuil, le statut des caisses concernées passe à 'gelée'.
    */
    'freeze_accounts_on_alert' => env('AUDIT_FREEZE_ON_ALERT', false),

    /*
    |--------------------------------------------------------------------------
    | Export Merkle root (optionnel)
    |--------------------------------------------------------------------------
    | Exporter la racine Merkle vers un fichier ou S3 pour preuve externe.
    | driver: null | 'file' | 's3'
    */
    'merkle_export_driver' => env('AUDIT_MERKLE_EXPORT', null),
    'merkle_export_path'   => env('AUDIT_MERKLE_EXPORT_PATH', storage_path('app/audit_merkle')),
];

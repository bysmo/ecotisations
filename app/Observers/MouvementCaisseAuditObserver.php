<?php

namespace App\Observers;

use App\Models\MouvementCaisse;
use App\Services\AuditFinancierService;

/**
 * Enregistre chaque création de MouvementCaisse dans le journal audit_financier (append-only).
 */
class MouvementCaisseAuditObserver
{
    public function __construct(
        protected AuditFinancierService $audit
    ) {}

    public function created(MouvementCaisse $mouvement): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('audit_financier')) {
            return;
        }
        try {
            $montant = (float) $mouvement->montant;
            $this->audit->appendMouvement(
                $mouvement->type,
                $montant,
                $mouvement->caisse_id,
                $mouvement->sens,
                $mouvement->reference_type,
                $mouvement->reference_id
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Audit financier: échec enregistrement mouvement ' . $mouvement->id, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_caisse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('cascade');

            // Ex: paiement, approvisionnement, transfert_out, transfert_in, annulation_paiement...
            $table->string('type', 50);
            $table->enum('sens', ['entree', 'sortie']);
            $table->decimal('montant', 15, 0);
            $table->timestamp('date_operation')->useCurrent();
            $table->string('libelle')->nullable();
            $table->text('notes')->nullable();

            // Référence vers l'objet source (Paiement / Approvisionnement / Transfert ...)
            $table->nullableMorphs('reference'); // reference_type, reference_id

            $table->timestamps();

            $table->index(['caisse_id', 'date_operation']);
            $table->index(['type', 'sens']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_caisse');
    }
};


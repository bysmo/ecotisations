<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('fin_mois_logs');
        
        Schema::create('fin_mois_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membre_id');
            $table->date('periode_debut'); // Mois traité
            $table->date('periode_fin'); // Fin du mois traité
            $table->string('email_destinataire');
            $table->string('sujet_email');
            $table->text('corps_email');
            $table->enum('statut', ['en_attente', 'envoye', 'echec'])->default('en_attente');
            $table->text('erreur')->nullable();
            $table->timestamp('envoye_at')->nullable();
            $table->unsignedBigInteger('envoye_par'); // Admin qui a lancé le traitement
            $table->json('resume_paiements')->nullable(); // Résumé des paiements en JSON
            $table->integer('nombre_paiements')->default(0);
            $table->decimal('montant_total', 15, 0)->default(0);
            $table->timestamps();
            
            $table->index('membre_id');
            $table->index('statut');
            $table->index(['periode_debut', 'periode_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mois_logs');
    }
};

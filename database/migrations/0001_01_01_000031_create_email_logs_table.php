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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'campagne', 'paiement', 'engagement', 'fin_mois', 'rappel', etc.
            $table->foreignId('campagne_id')->nullable()->constrained('email_campaigns')->onDelete('set null');
            $table->foreignId('membre_id')->nullable()->constrained('membres')->onDelete('set null');
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->foreignId('engagement_id')->nullable()->constrained('engagements')->onDelete('set null');
            $table->string('destinataire_email');
            $table->string('sujet');
            $table->text('message');
            $table->enum('statut', ['en_attente', 'envoye', 'echec'])->default('en_attente');
            $table->text('erreur')->nullable();
            $table->timestamp('envoye_at')->nullable();
            $table->json('metadata')->nullable(); // Pour stocker des infos supplémentaires
            $table->timestamps();
            
            $table->index('type');
            $table->index('statut');
            $table->index('campagne_id');
            $table->index('membre_id');
            $table->index('envoye_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};

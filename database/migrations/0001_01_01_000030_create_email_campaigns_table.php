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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('sujet');
            $table->text('message');
            $table->enum('statut', ['brouillon', 'en_cours', 'terminee', 'annulee'])->default('brouillon');
            $table->json('filtres')->nullable(); // Stocke les critères de filtrage (statut, cotisation_id, date_adhesion, etc.)
            $table->integer('total_destinataires')->default(0);
            $table->integer('envoyes')->default(0);
            $table->integer('echecs')->default(0);
            $table->foreignId('cree_par')->constrained('users')->onDelete('cascade');
            $table->timestamp('envoyee_at')->nullable();
            $table->timestamps();
            
            $table->index('statut');
            $table->index('cree_par');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};

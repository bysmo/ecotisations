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
        Schema::create('epargne_retrait_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('souscription_id')->constrained('epargne_souscriptions')->cascadeOnDelete();
            $table->foreignId('membre_id')->constrained('membres')->cascadeOnDelete();
            $table->longText('montant_demande')->nullable();
            $table->string('statut', 20)->default('en_attente'); // en_attente, traite, rejete
            $table->foreignId('traite_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('mode_retrait', 30)->default('virement_interne'); // virement_interne, pispi
            $table->text('checksum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epargne_retrait_demandes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotisation_versement_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotisation_id')->constrained('cotisations')->cascadeOnDelete();
            $table->foreignId('caisse_id')->constrained('caisses')->cascadeOnDelete();
            $table->foreignId('demande_par_membre_id')->constrained('membres')->cascadeOnDelete();
            $table->decimal('montant_demande', 15, 0)->default(0);
            $table->string('statut', 20)->default('en_attente'); // en_attente, traite, rejete
            $table->foreignId('traite_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotisation_versement_demandes');
    }
};

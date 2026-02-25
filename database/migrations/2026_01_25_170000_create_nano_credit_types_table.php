<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nano_credit_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('montant_min', 15, 0);
            $table->decimal('montant_max', 15, 0)->nullable();
            $table->unsignedSmallInteger('duree_jours');
            $table->decimal('taux_interet', 5, 2)->default(0)->comment('Taux annuel en %');
            $table->enum('frequence_remboursement', ['journalier', 'hebdomadaire', 'mensuel', 'trimestriel'])->default('hebdomadaire');
            $table->boolean('actif')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nano_credit_types');
    }
};

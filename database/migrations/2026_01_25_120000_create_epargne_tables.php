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
        Schema::disableForeignKeyConstraints();

        // Plans d'épargne (définis par l'admin)
        Schema::create('epargne_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('montant_min', 15, 0)->default(0);
            $table->decimal('montant_max', 15, 0)->nullable();
            $table->enum('frequence', ['quotidien', 'hebdomadaire', 'mensuel', 'trimestriel']);
            $table->foreignId('caisse_id')->nullable()->constrained('caisses')->nullOnDelete();
            $table->boolean('actif')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });

        // Souscriptions des membres à un plan
        Schema::create('epargne_souscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained('membres')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('epargne_plans')->cascadeOnDelete();
            $table->decimal('montant', 15, 0);
            $table->date('date_debut');
            $table->unsignedTinyInteger('jour_du_mois')->nullable(); // 1-28 pour mensuel
            $table->enum('statut', ['active', 'suspendue', 'cloturee'])->default('active');
            $table->decimal('solde_courant', 15, 0)->default(0);
            $table->timestamps();
        });

        // Échéances (une par date de versement attendu)
        Schema::create('epargne_echeances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('souscription_id')->constrained('epargne_souscriptions')->cascadeOnDelete();
            $table->date('date_echeance');
            $table->decimal('montant', 15, 0);
            $table->enum('statut', ['a_venir', 'payee', 'en_retard', 'annulee'])->default('a_venir');
            $table->timestamp('paye_le')->nullable();
            $table->timestamps();
        });

        // Versements effectués
        Schema::create('epargne_versements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('souscription_id')->constrained('epargne_souscriptions')->cascadeOnDelete();
            $table->foreignId('echeance_id')->nullable()->constrained('epargne_echeances')->nullOnDelete();
            $table->foreignId('membre_id')->constrained('membres')->cascadeOnDelete();
            $table->decimal('montant', 15, 0);
            $table->date('date_versement');
            $table->string('mode_paiement')->default('paydunya'); // paydunya, especes, virement...
            $table->string('reference')->nullable();
            $table->foreignId('caisse_id')->nullable()->constrained('caisses')->nullOnDelete();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('epargne_versements');
        Schema::dropIfExists('epargne_echeances');
        Schema::dropIfExists('epargne_souscriptions');
        Schema::dropIfExists('epargne_plans');
        Schema::enableForeignKeyConstraints();
    }
};

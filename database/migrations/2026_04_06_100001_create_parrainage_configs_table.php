<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parrainage_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('actif')->default(false)->comment('Activer/désactiver le système de parrainage');
            $table->enum('type_remuneration', ['fixe', 'pourcentage'])->default('fixe')
                  ->comment('Type de rémunération : montant fixe ou pourcentage');
            $table->decimal('montant_remuneration', 15, 2)->default(0)
                  ->comment('Montant fixe OU pourcentage (ex: 5.00 pour 5%)');
            $table->enum('declencheur', ['inscription', 'premier_paiement', 'adhesion_cotisation'])
                  ->default('inscription')
                  ->comment('Événement déclenchant la commission');
            $table->integer('delai_validation_jours')->default(0)
                  ->comment('Délai en jours avant de pouvoir réclamer la commission (0 = immédiat)');
            $table->integer('niveaux_parrainage')->default(1)
                  ->comment('Nombre de niveaux de parrainage (1 = direct uniquement)');
            $table->decimal('taux_niveau_2', 5, 2)->default(0)
                  ->comment('Taux/montant pour le niveau 2 (parrain du parrain)');
            $table->decimal('taux_niveau_3', 5, 2)->default(0)
                  ->comment('Taux/montant pour le niveau 3');
            $table->text('description')->nullable()
                  ->comment('Description du programme de parrainage affichée aux membres');
            $table->integer('min_filleuls_retrait')->default(1)
                  ->comment('Nombre minimum de filleuls validés pour pouvoir réclamer');
            $table->decimal('montant_min_retrait', 15, 2)->default(0)
                  ->comment('Montant minimum cumulé pour pouvoir réclamer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parrainage_configs');
    }
};

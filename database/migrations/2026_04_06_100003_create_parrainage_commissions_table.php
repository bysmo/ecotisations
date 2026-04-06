<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parrainage_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parrain_id')->comment('Membre qui a parrainé');
            $table->unsignedBigInteger('filleul_id')->comment('Membre recruté (filleul)');
            $table->integer('niveau')->default(1)->comment('Niveau de parrainage (1=direct, 2=indirect, etc.)');
            $table->decimal('montant', 15, 2)->default(0)->comment('Montant de la commission générée');
            $table->enum('statut', ['en_attente', 'disponible', 'reclame', 'paye', 'annule'])
                  ->default('en_attente')
                  ->comment('Statut de la commission');
            $table->enum('declencheur', ['inscription', 'premier_paiement', 'adhesion_cotisation'])
                  ->default('inscription')
                  ->comment('Événement qui a déclenché cette commission');
            $table->timestamp('disponible_le')->nullable()
                  ->comment('Date à partir de laquelle la commission est disponible');
            $table->timestamp('reclame_le')->nullable()
                  ->comment('Date de réclamation par le parrain');
            $table->timestamp('paye_le')->nullable()
                  ->comment('Date de paiement effectif');
            $table->unsignedBigInteger('traite_par')->nullable()
                  ->comment('Administrateur ayant traité la réclamation');
            $table->text('note_admin')->nullable()
                  ->comment('Note de l\'administrateur lors du traitement');
            $table->string('reference')->nullable()->unique()
                  ->comment('Référence unique de la commission');
            $table->foreign('parrain_id')->references('id')->on('membres')->cascadeOnDelete();
            $table->foreign('filleul_id')->references('id')->on('membres')->cascadeOnDelete();
            $table->foreign('traite_par')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->index(['parrain_id', 'statut']);
            $table->index(['filleul_id']);
            $table->index(['statut', 'disponible_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parrainage_commissions');
    }
};

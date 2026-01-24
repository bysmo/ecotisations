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
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->foreignId('membre_id')->constrained('membres')->onDelete('cascade');
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('cascade');
            $table->string('type'); // mensuelle, annuelle, exceptionnelle
            $table->decimal('montant', 15, 0);
            $table->date('date_echeance');
            $table->date('date_paiement')->nullable();
            $table->enum('statut', ['payee', 'en_attente', 'en_retard'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};

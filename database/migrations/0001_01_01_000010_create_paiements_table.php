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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->foreignId('membre_id')->constrained('membres')->onDelete('cascade');
            $table->foreignId('cotisation_id')->constrained('cotisations')->onDelete('cascade');
            $table->foreignId('caisse_id')->constrained('caisses')->onDelete('cascade');
            $table->decimal('montant', 15, 0);
            $table->date('date_paiement');
            $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'mobile_money', 'autre'])->default('especes');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

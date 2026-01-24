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
        Schema::create('engagements', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->foreignId('membre_id')->constrained('membres')->onDelete('cascade');
            $table->foreignId('cotisation_id')->constrained('cotisations')->onDelete('cascade');
            $table->decimal('montant_engage', 15, 0);
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagements');
    }
};

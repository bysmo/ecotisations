<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotisation_adhesions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained('membres')->cascadeOnDelete();
            $table->foreignId('cotisation_id')->constrained('cotisations')->cascadeOnDelete();
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable();
            $table->text('commentaire_admin')->nullable();
            $table->timestamps();
            $table->unique(['membre_id', 'cotisation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotisation_adhesions');
    }
};

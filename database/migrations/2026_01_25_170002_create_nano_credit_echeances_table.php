<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nano_credit_echeances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nano_credit_id')->constrained('nano_credits')->cascadeOnDelete();
            $table->date('date_echeance');
            $table->decimal('montant', 15, 0);
            $table->string('statut', 20)->default('a_venir'); // a_venir, payee, en_retard
            $table->timestamp('paye_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nano_credit_echeances');
    }
};

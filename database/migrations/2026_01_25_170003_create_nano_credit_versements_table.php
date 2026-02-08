<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nano_credit_versements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nano_credit_id')->constrained('nano_credits')->cascadeOnDelete();
            $table->foreignId('nano_credit_echeance_id')->nullable()->constrained('nano_credit_echeances')->nullOnDelete();
            $table->decimal('montant', 15, 0);
            $table->date('date_versement');
            $table->string('mode_paiement')->default('especes');
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nano_credit_versements');
    }
};

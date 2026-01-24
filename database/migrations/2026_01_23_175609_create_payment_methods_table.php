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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom affiché (ex: "PayDunya", "PayPal", "Stripe")
            $table->string('code')->unique(); // Code unique (ex: "paydunya", "paypal", "stripe")
            $table->string('icon')->nullable(); // Icône Bootstrap Icons
            $table->text('description')->nullable(); // Description du moyen de paiement
            $table->boolean('enabled')->default(false); // Actif/Inactif
            $table->json('config')->nullable(); // Configuration spécifique (clés API, etc.)
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};

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

        Schema::create('nano_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained('membres')->onDelete('cascade');
            $table->decimal('montant', 15, 0);
            $table->string('telephone'); // numéro sans indicatif (account_alias)
            $table->string('withdraw_mode', 50); // orange-money-senegal, wave-senegal, etc.
            $table->string('statut', 20)->default('created'); // created, pending, success, failed
            $table->string('disburse_token')->nullable();
            $table->string('disburse_id')->nullable()->comment('Référence métier (ex: id nano crédit)');
            $table->string('transaction_id')->nullable();
            $table->string('provider_ref')->nullable();
            $table->boolean('callback_received')->default(false);
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('nano_credits');
        Schema::enableForeignKeyConstraints();
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des alias de portefeuille des membres
        Schema::create('membre_wallet_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained('membres')->onDelete('cascade');
            $table->string('alias')->unique(); // L'UUID fourni par la banque
            $table->string('label')->nullable(); // Ex: "Mon compte BOA", "Portefeuille Wave"
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Table des alias opérationnels (Bénéficiaires Serenity)
        Schema::create('pispi_operation_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type')->unique(); // cagnotte, tontine, nano_credit
            $table->string('alias'); // L'UUID du compte Serenity pour cette opération
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Ajouter la référence de l'alias utilisé dans les paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('wallet_alias_id')->nullable()->after('caisse_id')->constrained('membre_wallet_aliases')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['wallet_alias_id']);
            $table->dropColumn('wallet_alias_id');
        });
        Schema::dropIfExists('pispi_operation_aliases');
        Schema::dropIfExists('membre_wallet_aliases');
    }
};

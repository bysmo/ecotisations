<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On rend d'abord l'email nullable
        Schema::table('membres', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // On s'assure qu'aucun téléphone n'est nul avant de mettre la contrainte
        // (Pour les membres existants sans téléphone, on met le numéro de membre temporairement)
        DB::statement("UPDATE membres SET telephone = numero WHERE telephone IS NULL OR telephone = ''");

        Schema::table('membres', function (Blueprint $table) {
            $table->string('telephone')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->dropUnique(['telephone']);
            $table->string('telephone')->nullable()->change();
        });
    }
};

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
        Schema::table('membres', function (Blueprint $table) {
            // PIN de sécurité hashé (null si non défini)
            $table->string('code_pin')->nullable()->after('password');
            // Date de création/modification du PIN
            $table->timestamp('code_pin_created_at')->nullable()->after('code_pin');
            // Compteur de tentatives échouées
            $table->unsignedTinyInteger('pin_attempts')->default(0)->after('code_pin_created_at');
            // Timestamp de verrouillage temporaire
            $table->timestamp('pin_locked_until')->nullable()->after('pin_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn(['code_pin', 'code_pin_created_at', 'pin_attempts', 'pin_locked_until']);
        });
    }
};

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
            // L'utilisateur choisit d'activer ou non le PIN (défaut : désactivé)
            $table->boolean('pin_enabled')->default(false)->after('pin_locked_until');
            // Mode A = demandé à chaque opération, Mode B = session 5 minutes
            $table->enum('pin_mode', ['each_time', 'session'])->default('each_time')->after('pin_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn(['pin_enabled', 'pin_mode']);
        });
    }
};

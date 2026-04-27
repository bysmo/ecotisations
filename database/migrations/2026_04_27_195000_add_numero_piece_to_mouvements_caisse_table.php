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
        Schema::table('mouvements_caisse', function (Blueprint $table) {
            $table->string('numero_piece')->nullable()->after('id');
            $table->index('numero_piece');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvements_caisse', function (Blueprint $table) {
            $table->dropColumn('numero_piece');
        });
    }
};

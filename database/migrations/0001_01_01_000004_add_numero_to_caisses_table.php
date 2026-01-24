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
        if (Schema::hasTable('caisses') && !Schema::hasColumn('caisses', 'numero')) {
            Schema::table('caisses', function (Blueprint $table) {
                $table->string('numero', 9)->unique()->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};

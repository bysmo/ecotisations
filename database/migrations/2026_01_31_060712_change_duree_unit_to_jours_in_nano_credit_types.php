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
        Schema::table('nano_credit_types', function (Blueprint $table) {
            $table->renameColumn('duree_mois', 'duree_jours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nano_credit_types', function (Blueprint $table) {
            $table->renameColumn('duree_jours', 'duree_mois');
        });
    }
};

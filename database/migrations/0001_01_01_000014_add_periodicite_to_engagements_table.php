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
        Schema::table('engagements', function (Blueprint $table) {
            if (!Schema::hasColumn('engagements', 'periodicite')) {
                $table->enum('periodicite', ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle', 'unique'])->default('mensuelle')->after('montant_engage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            if (Schema::hasColumn('engagements', 'periodicite')) {
                $table->dropColumn('periodicite');
            }
        });
    }
};

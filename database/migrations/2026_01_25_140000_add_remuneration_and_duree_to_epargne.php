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
        Schema::table('epargne_plans', function (Blueprint $table) {
            $table->decimal('taux_remuneration', 5, 2)->default(0)->after('frequence'); // ex: 3.50 pour 3,5%
            $table->unsignedSmallInteger('duree_mois')->default(12)->after('taux_remuneration'); // ex: 12, 24, 36
        });

        Schema::table('epargne_souscriptions', function (Blueprint $table) {
            $table->date('date_fin')->nullable()->after('date_debut'); // date de fin du plan (calculée à la souscription)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epargne_plans', function (Blueprint $table) {
            $table->dropColumn(['taux_remuneration', 'duree_mois']);
        });
        Schema::table('epargne_souscriptions', function (Blueprint $table) {
            $table->dropColumn('date_fin');
        });
    }
};

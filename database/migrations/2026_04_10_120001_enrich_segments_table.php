<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enrichit la table segments pour la segmentation clientèle complète :
 *   - slug      : identifiant URL unique (ex: 'commercant')
 *   - couleur   : code hex pour l'affichage UI (ex: '#f59e0b')
 *   - icone     : classe Bootstrap Icons (ex: 'bi bi-shop')
 *   - is_default: true pour le segment "NON CLASSÉ"
 *   - actif     : permet de désactiver un segment sans le supprimer
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('nom');
            $table->string('couleur', 20)->default('#6b7280')->after('description');
            $table->string('icone', 60)->default('bi bi-people')->after('couleur');
            $table->boolean('is_default')->default(false)->after('icone');
            $table->boolean('actif')->default(true)->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->dropColumn(['slug', 'couleur', 'icone', 'is_default', 'actif']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute segment_id (FK → segments.id) à la table membres.
 *
 * Le segment remplace l'ancien champ string 'segment' (déjà supprimé).
 * Un segment par défaut "NON CLASSÉ" sera créé par le seeder.
 * NullOnDelete : si un segment est supprimé, le membre devient non classé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->foreignId('segment_id')
                  ->nullable()
                  ->after('statut')
                  ->constrained('segments')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Segment::class);
            $table->dropColumn('segment_id');
        });
    }
};

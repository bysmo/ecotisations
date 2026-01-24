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
        // Migrer les segments existants de la table membres vers la table segments
        $segmentsExistants = DB::table('membres')
            ->select('segment')
            ->whereNotNull('segment')
            ->where('segment', '!=', '')
            ->distinct()
            ->pluck('segment');
        
        foreach ($segmentsExistants as $segmentNom) {
            // Vérifier si le segment n'existe pas déjà
            $exists = DB::table('segments')->where('nom', $segmentNom)->exists();
            if (!$exists) {
                DB::table('segments')->insert([
                    'nom' => $segmentNom,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire en cas de rollback
    }
};

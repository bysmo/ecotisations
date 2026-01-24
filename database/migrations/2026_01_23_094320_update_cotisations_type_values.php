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
        // Mettre à jour les valeurs du champ type dans la table cotisations
        // mensuelle -> reguliere
        // annuelle -> ponctuelle
        // exceptionnelle reste exceptionnelle
        
        DB::table('cotisations')
            ->where('type', 'mensuelle')
            ->update(['type' => 'reguliere']);
        
        DB::table('cotisations')
            ->where('type', 'annuelle')
            ->update(['type' => 'ponctuelle']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer les anciennes valeurs
        DB::table('cotisations')
            ->where('type', 'reguliere')
            ->update(['type' => 'mensuelle']);
        
        DB::table('cotisations')
            ->where('type', 'ponctuelle')
            ->update(['type' => 'annuelle']);
    }
};

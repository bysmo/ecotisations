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
        // Corriger toutes les valeurs incorrectes dans le champ type
        // Les valeurs qui sont des fréquences doivent être mappées vers les bons types
        
        // trimestrielle, semestrielle -> reguliere (cotisations régulières)
        DB::table('cotisations')
            ->whereIn('type', ['trimestrielle', 'semestrielle'])
            ->update(['type' => 'reguliere']);
        
        // unique -> ponctuelle (cotisation ponctuelle)
        DB::table('cotisations')
            ->where('type', 'unique')
            ->update(['type' => 'ponctuelle']);
        
        // Au cas où il y aurait encore des anciennes valeurs
        DB::table('cotisations')
            ->where('type', 'mensuelle')
            ->update(['type' => 'reguliere']);
        
        DB::table('cotisations')
            ->where('type', 'annuelle')
            ->update(['type' => 'ponctuelle']);
        
        // S'assurer qu'il n'y a que les 3 valeurs valides
        // Si une valeur n'est pas dans la liste, on la met à 'reguliere' par défaut
        $validTypes = ['reguliere', 'ponctuelle', 'exceptionnelle'];
        $allTypes = DB::table('cotisations')->select('type')->distinct()->pluck('type')->toArray();
        
        foreach ($allTypes as $type) {
            if (!in_array($type, $validTypes)) {
                DB::table('cotisations')
                    ->where('type', $type)
                    ->update(['type' => 'reguliere']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être inversée de manière fiable
        // car on ne sait pas quelle était la valeur originale
    }
};

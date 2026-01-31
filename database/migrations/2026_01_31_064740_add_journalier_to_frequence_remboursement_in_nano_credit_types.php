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
        // Utilisation de DB::statement car modifier un enum via Blueprint peut être capricieux selon la version de MySQL
        DB::statement("ALTER TABLE nano_credit_types MODIFY COLUMN frequence_remboursement ENUM('journalier', 'hebdomadaire', 'mensuel', 'trimestriel') DEFAULT 'hebdomadaire'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE nano_credit_types MODIFY COLUMN frequence_remboursement ENUM('hebdomadaire', 'mensuel', 'trimestriel') DEFAULT 'mensuel'");
    }
};

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
        // For MySQL/MariaDB, we can modify the enum
        // If it's SQLite (used in tests), it might be more complex, but standard Laravel migration should handle it or we use a more generic approach
        
        Schema::table('email_templates', function (Blueprint $table) {
            // First we need to check the current driver
            $driver = DB::getDriverName();
            
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement("ALTER TABLE email_templates MODIFY COLUMN type ENUM('paiement', 'engagement', 'activation', 'autre') DEFAULT 'paiement'");
            }
            // For other drivers (like sqlite), Laravel's change() method is usually better but requires doctrine/dbal
            // However, many projects use string' instead of enum to avoid these issues.
            // Since this is a specific project, I'll stick to a safe approach.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement("ALTER TABLE email_templates MODIFY COLUMN type ENUM('paiement', 'engagement', 'autre') DEFAULT 'paiement'");
            }
        });
    }
};

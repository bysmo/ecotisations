<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table epargne_souscriptions (CMA/Tontine)
        if (Schema::hasTable('epargne_souscriptions')) {
            Schema::table('epargne_souscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('epargne_souscriptions', 'caisse_id')) {
                    $table->foreignId('caisse_id')->nullable()->constrained('caisses')->onDelete('set null');
                }
            });
        }

        // Table nano_credits
        if (Schema::hasTable('nano_credits')) {
            Schema::table('nano_credits', function (Blueprint $table) {
                if (!Schema::hasColumn('nano_credits', 'compte_remboursement_id')) {
                    $table->foreignId('compte_remboursement_id')->nullable()->constrained('caisses')->onDelete('set null');
                }
                if (!Schema::hasColumn('nano_credits', 'compte_credit_id')) {
                    $table->foreignId('compte_credit_id')->nullable()->constrained('caisses')->onDelete('set null');
                }
                if (!Schema::hasColumn('nano_credits', 'compte_impaye_id')) {
                    $table->foreignId('compte_impaye_id')->nullable()->constrained('caisses')->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('epargne_souscriptions')) {
            Schema::table('epargne_souscriptions', function (Blueprint $table) {
                $table->dropForeign(['caisse_id']);
                $table->dropColumn('caisse_id');
            });
        }

        if (Schema::hasTable('nano_credits')) {
            Schema::table('nano_credits', function (Blueprint $table) {
                $table->dropForeign(['compte_remboursement_id']);
                $table->dropForeign(['compte_credit_id']);
                $table->dropForeign(['compte_impaye_id']);
                $table->dropColumn(['compte_remboursement_id', 'compte_credit_id', 'compte_impaye_id']);
            });
        }
    }
};

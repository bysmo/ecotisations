<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nano_credits', function (Blueprint $table) {
            $table->foreignId('nano_credit_type_id')->nullable()->after('id')->constrained('nano_credit_types')->nullOnDelete();
            $table->date('date_octroi')->nullable()->after('error_message');
            $table->date('date_fin_remboursement')->nullable()->after('date_octroi');
        });
    }

    public function down(): void
    {
        Schema::table('nano_credits', function (Blueprint $table) {
            $table->dropForeign(['nano_credit_type_id']);
            $table->dropColumn(['date_octroi', 'date_fin_remboursement']);
        });
    }
};

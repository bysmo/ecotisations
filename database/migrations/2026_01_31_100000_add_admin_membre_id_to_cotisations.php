<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->foreignId('admin_membre_id')->nullable()->after('created_by_membre_id')->constrained('membres')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropForeign(['admin_membre_id']);
            $table->dropColumn('admin_membre_id');
        });
    }
};

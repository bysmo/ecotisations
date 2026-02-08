<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('numero');
            $table->foreignId('created_by_membre_id')->nullable()->after('caisse_id')->constrained('membres')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropForeign(['created_by_membre_id']);
            $table->dropColumn(['code', 'created_by_membre_id']);
        });
    }
};

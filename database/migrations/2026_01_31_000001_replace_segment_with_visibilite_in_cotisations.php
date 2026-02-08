<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropColumn('segment');
        });
        Schema::table('cotisations', function (Blueprint $table) {
            $table->enum('visibilite', ['publique', 'privee'])->default('publique')->after('tag');
        });
    }

    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropColumn('visibilite');
        });
        Schema::table('cotisations', function (Blueprint $table) {
            $table->string('segment')->nullable()->after('tag');
        });
    }
};

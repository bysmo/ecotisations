<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->string('code_parrainage', 12)->nullable()->unique()->after('statut')
                  ->comment('Code unique de parrainage du membre');
            $table->unsignedBigInteger('parrain_id')->nullable()->after('code_parrainage')
                  ->comment('ID du membre parrain (qui a recruté ce membre)');
            $table->foreign('parrain_id')->references('id')->on('membres')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropForeign(['parrain_id']);
            $table->dropColumn(['code_parrainage', 'parrain_id']);
        });
    }
};

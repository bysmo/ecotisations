<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->date('date_naissance')->nullable()->after('prenom');
            $table->string('lieu_naissance')->nullable()->after('date_naissance');
            $table->enum('sexe', ['M', 'F', 'Autre'])->nullable()->after('lieu_naissance');
            $table->string('nom_mere')->nullable()->after('sexe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn(['date_naissance', 'lieu_naissance', 'sexe', 'nom_mere']);
        });
    }
};

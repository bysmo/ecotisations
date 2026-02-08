<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->string('metier')->nullable()->after('adresse_kyc');
            $table->string('localisation')->nullable()->after('metier');
            $table->string('contact_1')->nullable()->after('localisation');
            $table->string('contact_2')->nullable()->after('contact_1');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn(['metier', 'localisation', 'contact_1', 'contact_2']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kyc_verification_id')->constrained('kyc_verifications')->onDelete('cascade');
            $table->string('type'); // piece_identite, justificatif_domicile, autre
            $table->string('path');
            $table->string('nom_original');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('kyc_documents');
        Schema::enableForeignKeyConstraints();
    }
};

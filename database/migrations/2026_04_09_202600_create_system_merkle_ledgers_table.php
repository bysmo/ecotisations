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
        Schema::create('system_merkle_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->unsignedBigInteger('record_id');
            $table->string('action'); // created, updated, deleted
            $table->string('record_checksum')->nullable();
            $table->string('previous_hash')->nullable();
            $table->string('hash_chain');
            $table->timestamps();

            // Indexes pour l'analyse rapide
            $table->index(['table_name', 'record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_merkle_ledgers');
    }
};

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
        Schema::create('paydunya_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('master_key')->nullable();
            $table->text('private_key')->nullable();
            $table->text('public_key')->nullable();
            $table->text('token')->nullable();
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->string('ipn_url')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paydunya_configurations');
    }
};

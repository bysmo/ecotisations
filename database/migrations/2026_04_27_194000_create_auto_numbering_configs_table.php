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
        Schema::create('auto_numbering_configs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('object_type')->unique(); // e.g., 'membre', 'caisse', 'transaction', 'piece'
            $blueprint->string('description')->nullable();
            $blueprint->json('definition'); // [ {"type": "constant", "value": "CLT"}, {"type": "separator", "value": "-"}, {"type": "date", "value": "Ymd"}, ... ]
            $blueprint->integer('current_value')->default(0);
            $blueprint->boolean('is_active')->default(true);
            $blueprint->string('checksum')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_numbering_configs');
    }
};

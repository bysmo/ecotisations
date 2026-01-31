<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nano_credits', function (Blueprint $table) {
            $table->string('telephone')->nullable()->change();
            $table->string('withdraw_mode', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nano_credits', function (Blueprint $table) {
            $table->string('telephone')->nullable(false)->change();
            $table->string('withdraw_mode', 50)->nullable(false)->change();
        });
    }
};

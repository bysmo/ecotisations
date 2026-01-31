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
            // Verification
            //$table->timestamp('email_verified_at')->nullable()->after('email');
            $table->timestamp('sms_verified_at')->nullable()->after('telephone');
            $table->string('verification_code', 10)->nullable()->after('statut');
            
            // KYC
            $table->string('piece_identite_recto')->nullable()->after('verification_code');
            $table->string('piece_identite_verso')->nullable()->after('piece_identite_recto');
            $table->string('selfie')->nullable()->after('piece_identite_verso');
            $table->decimal('latitude', 10, 8)->nullable()->after('adresse');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Security / MFA
            $table->boolean('mfa_enabled')->default(false)->after('password');
            $table->string('mfa_method')->default('sms')->after('mfa_enabled'); // sms, biometric
            $table->string('biometric_token')->nullable()->after('mfa_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn([
                //'email_verified_at',
                'sms_verified_at',
                'verification_code',
                'piece_identite_recto',
                'piece_identite_verso',
                'selfie',
                'latitude',
                'longitude',
                'mfa_enabled',
                'mfa_method',
                'biometric_token'
            ]);
        });
    }
};

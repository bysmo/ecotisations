<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer les moyens de paiement de base s'ils n'existent pas
        $paymentMethods = [
            [
                'name' => 'PayDunya',
                'code' => 'paydunya',
                'icon' => 'bi bi-phone',
                'description' => 'Paiement mobile money pour l\'Afrique de l\'Ouest',
                'enabled' => false,
                'config' => null,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'icon' => 'bi bi-paypal',
                'description' => 'Paiement en ligne international via PayPal',
                'enabled' => false,
                'config' => null,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'icon' => 'bi bi-credit-card-2-front',
                'description' => 'Paiement par carte bancaire via Stripe',
                'enabled' => false,
                'config' => null,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                $method
            );
        }

        // Migrer la configuration PayDunya existante vers payment_methods
        $paydunyaConfig = DB::table('paydunya_configurations')->first();
        if ($paydunyaConfig) {
            $config = [
                'master_key' => $paydunyaConfig->master_key,
                'private_key' => $paydunyaConfig->private_key,
                'public_key' => $paydunyaConfig->public_key,
                'token' => $paydunyaConfig->token,
                'mode' => $paydunyaConfig->mode,
                'ipn_url' => $paydunyaConfig->ipn_url,
            ];

            DB::table('payment_methods')
                ->where('code', 'paydunya')
                ->update([
                    'enabled' => $paydunyaConfig->enabled,
                    'config' => json_encode($config),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire en cas de rollback
    }
};

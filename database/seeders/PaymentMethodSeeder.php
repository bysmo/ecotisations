<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'PayDunya',
                'code' => 'paydunya',
                'icon' => 'bi bi-phone',
                'description' => 'Paiement mobile money pour l\'Afrique de l\'Ouest',
                'enabled' => false,
                'config' => null,
                'order' => 1,
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'icon' => 'bi bi-paypal',
                'description' => 'Paiement en ligne international via PayPal',
                'enabled' => false,
                'config' => null,
                'order' => 2,
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'icon' => 'bi bi-credit-card-2-front',
                'description' => 'Paiement par carte bancaire via Stripe',
                'enabled' => false,
                'config' => null,
                'order' => 3,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}

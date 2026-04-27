<?php

namespace Database\Seeders;

use App\Models\AutoNumberingConfig;
use Illuminate\Database\Seeder;

class AutoNumberingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        $configs = [
            [
                'object_type' => 'client',
                'description' => 'Numérotation des clients',
                'definition' => [
                    ['type' => 'constant', 'value' => 'CLT'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'date', 'value' => 'Y'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'sequence', 'length' => 5],
                ],
                'current_value' => 0,
                'is_active' => true,
            ],
            [
                'object_type' => 'compte',
                'description' => 'Numérotation des comptes bancaires',
                'definition' => [
                    ['type' => 'constant', 'value' => 'CPT'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'date', 'value' => 'Ym'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'sequence', 'length' => 6],
                ],
                'current_value' => 0,
                'is_active' => true,
            ],
            [
                'object_type' => 'piece_comptable',
                'description' => 'Numérotation des pièces comptables',
                'definition' => [
                    ['type' => 'constant', 'value' => 'PC'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'date', 'value' => 'Ymd'],
                    ['type' => 'separator', 'value' => '-'],
                    ['type' => 'sequence', 'length' => 7],
                ],
                'current_value' => 0,
                'is_active' => true,
            ],
            [
                'object_type' => 'transaction',
                'description' => 'Numérotation des transactions',
                'definition' => [
                    ['type' => 'constant', 'value' => 'TXN'],
                    ['type' => 'separator', 'value' => '_'],
                    ['type' => 'date', 'value' => 'Ymd'],
                    ['type' => 'separator', 'value' => '_'],
                    ['type' => 'sequence', 'length' => 8],
                ],
                'current_value' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($configs as $config) {
            AutoNumberingConfig::updateOrCreate(
                ['object_type' => $config['object_type']],
                $config
            );
        }
    }

    /**
     * Alias for up() to match Seeder convention if needed, 
     * but run() is the standard method for Seeders.
     */
    public function run(): void
    {
        $this->up();
    }
}

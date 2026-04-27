<?php

namespace App\Services;

use App\Models\AutoNumberingConfig;
use Illuminate\Support\Facades\DB;
use Exception;

class AutoNumberingService
{
    /**
     * Generate the next number for a given object type.
     *
     * @param string $objectType
     * @return string
     * @throws Exception
     */
    public function generate(string $objectType): string
    {
        return DB::transaction(function () use ($objectType) {
            $config = AutoNumberingConfig::where('object_type', $objectType)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$config) {
                // Fallback or Exception? Let's throw an exception to ensure configuration is set.
                throw new Exception("Aucune configuration de numérotation active trouvée pour : {$objectType}");
            }

            $blocks = $config->definition;
            $result = "";

            foreach ($blocks as $block) {
                switch ($block['type']) {
                    case 'constant':
                        $result .= $block['value'] ?? '';
                        break;
                    case 'date':
                        $result .= now()->format($block['value'] ?? 'Ymd');
                        break;
                    case 'separator':
                        $result .= $block['value'] ?? '';
                        break;
                    case 'sequence':
                        $nextVal = $config->incrementSequence();
                        $length = $block['length'] ?? 0;
                        $result .= str_pad((string) $nextVal, $length, '0', STR_PAD_LEFT);
                        break;
                    case 'string': // User mentioned "string" in blocks
                        $result .= $block['value'] ?? '';
                        break;
                }
            }

            return $result;
        });
    }

    /**
     * Preview what the next number would look like (without incrementing).
     *
     * @param string $objectType
     * @return string
     */
    public function preview(string $objectType): string
    {
        $config = AutoNumberingConfig::where('object_type', $objectType)
            ->first();

        if (!$config) {
            return "Non configuré";
        }

        $blocks = $config->definition;
        $result = "";

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'constant':
                case 'string':
                    $result .= $block['value'] ?? '';
                    break;
                case 'date':
                    $result .= now()->format($block['value'] ?? 'Ymd');
                    break;
                case 'separator':
                    $result .= $block['value'] ?? '';
                    break;
                case 'sequence':
                    $nextVal = $config->current_value + 1;
                    $length = $block['length'] ?? 0;
                    $result .= str_pad((string) $nextVal, $length, '0', STR_PAD_LEFT);
                    break;
            }
        }

        return $result;
    }
}

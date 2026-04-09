<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HasChecksum
{
    /**
     * Hook Eloquent events to automatically manage the checksum.
     */
    public static function bootHasChecksum(): void
    {
        static::saving(function ($model) {
            $model->checksum = $model->calculateChecksum();
        });

        static::created(function ($model) {
            $model->appendToMerkleLedger('created');
        });

        static::updated(function ($model) {
            $model->appendToMerkleLedger('updated');
        });

        static::deleted(function ($model) {
            $model->appendToMerkleLedger('deleted');
        });
    }

    /**
     * Ajoute une entrée dans le Ledger Immuable (Hash Chain)
     */
    public function appendToMerkleLedger(string $action): void
    {
        // Si la table n'existe pas encore (ex: pendant que les migrations s'exécutent), on ignore
        if (!\Illuminate\Support\Facades\Schema::hasTable('system_merkle_ledgers')) {
            return;
        }

        $tableName = $this->getTable();
        $recordId = $this->getKey();
        $checksum = $action === 'deleted' ? null : $this->checksum;

        \Illuminate\Support\Facades\DB::transaction(function () use ($tableName, $recordId, $action, $checksum) {
            // Verrouillage exclusif pour éviter les forks de chaîne en haute concurrence
            $lastLedger = \App\Models\SystemMerkleLedger::lockForUpdate()->orderBy('id', 'desc')->first();
            $previousHash = $lastLedger ? $lastLedger->hash_chain : null;
            
            $key = config('app.key');
            $payload = implode('|', [
                $tableName,
                $recordId,
                $action,
                $checksum ?? '',
                $previousHash ?? ''
            ]);
            
            $hashChain = hash_hmac('sha256', $payload, $key);
            
            \App\Models\SystemMerkleLedger::create([
                'table_name' => $tableName,
                'record_id' => $recordId,
                'action' => $action,
                'record_checksum' => $checksum,
                'previous_hash' => $previousHash,
                'hash_chain' => $hashChain,
            ]);
        });
    }

    /**
     * Calculate the checksum for the model's current attributes.
     */
    public function calculateChecksum(): string
    {
        // Get all attributes except checksum, id (as it's null before insert), and timestamps
        $attributes = array_diff_key(
            $this->attributes,
            array_flip(['id', 'checksum', 'created_at', 'updated_at', 'deleted_at'])
        );

        // Normalize all attributes to strings to avoid type mismatch
        // PHP `json_encode` treats 1 and "1" differently, which fails checksums
        // between Seed/Save context (memory typed) and DB fetched context (string/PDO).
        $normalized = [];
        foreach ($attributes as $key => $value) {
            if (is_null($value)) {
                $normalized[$key] = null;
            } elseif (is_bool($value)) {
                $normalized[$key] = $value ? '1' : '0';
            } else {
                $normalized[$key] = (string) $value;
            }
        }

        // Sort keys to ensure consistent hashing
        ksort($normalized);

        // Convert to JSON string for hashing
        $data = json_encode($normalized);
        $key  = config('app.key');

        return hash_hmac('sha256', $data, $key);
    }

    /**
     * Verify the integrity of the model's data.
     */
    public function verifyChecksum(): bool
    {
        if (empty($this->checksum)) {
            return false;
        }

        return hash_equals($this->checksum, $this->calculateChecksum());
    }
}

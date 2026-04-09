<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMerkleLedger extends Model
{
    protected $table = 'system_merkle_ledgers';

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'record_checksum',
        'previous_hash',
        'hash_chain',
    ];
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $cotisations = DB::table('cotisations')->where(function ($q) {
            $q->whereNull('code')->orWhere('code', '');
        })->get();
        foreach ($cotisations as $cotisation) {
            do {
                $code = strtoupper(Str::random(6));
            } while (DB::table('cotisations')->where('code', $code)->exists());
            DB::table('cotisations')->where('id', $cotisation->id)->update(['code' => $code]);
        }
    }

    public function down(): void
    {
        // No rollback needed
    }
};

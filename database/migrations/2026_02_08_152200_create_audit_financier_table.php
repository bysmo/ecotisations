<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Table audit_financier : journal append-only des transactions.
 * - transaction_id (UUID), montant, compte_source/dest, hash_chain (SHA-256 ligne précédente), signature (HMAC).
 * MySQL : triggers pour interdire UPDATE/DELETE (équivalent RULE PostgreSQL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_financier', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id')->unique();
            $table->string('type_transaction', 50);
            $table->decimal('montant', 20, 4);
            $table->unsignedBigInteger('compte_source_id')->nullable();
            $table->unsignedBigInteger('compte_dest_id')->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('timestamp_precis', 17, 6)->nullable();
            $table->char('hash_chain', 64);
            $table->string('signature', 128);
            $table->timestamps();
            $table->index('created_at');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_financier_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_financier_no_delete');
            DB::unprepared("CREATE TRIGGER audit_financier_no_update BEFORE UPDATE ON audit_financier FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_financier is append-only'");
            DB::unprepared("CREATE TRIGGER audit_financier_no_delete BEFORE DELETE ON audit_financier FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_financier is append-only'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_financier_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_financier_no_delete');
        }
        Schema::dropIfExists('audit_financier');
    }
};

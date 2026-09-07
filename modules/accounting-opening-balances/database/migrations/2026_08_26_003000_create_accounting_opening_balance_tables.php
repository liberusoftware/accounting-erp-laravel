<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_opening_balance_batches', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('batch_ref', 190);
            $t->date('migration_date');
            $t->char('currency', 3)->nullable();
            $t->string('status', 24)->default('draft');
            $t->char('source_hash', 64);
            $t->string('idempotency_key', 190)->nullable();
            $t->json('summary')->nullable();
            $t->text('failure_message')->nullable();
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'batch_ref']);
            $t->index(['team_id', 'status', 'migration_date']);
        });
        Schema::create('accounting_opening_balance_entries', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_opening_balance_batches')->cascadeOnDelete();
            $t->string('balance_type', 24);
            $t->string('reference_type', 160);
            $t->string('reference_id', 190);
            $t->string('document_ref', 190)->nullable();
            $t->date('document_date')->nullable();
            $t->date('due_date')->nullable();
            $t->char('currency', 3);
            $t->decimal('debit_amount', 20, 2)->default(0);
            $t->decimal('credit_amount', 20, 2)->default(0);
            $t->string('status', 24)->default('pending');
            $t->string('description', 255)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['batch_id', 'balance_type', 'reference_id']);
            $t->index(['batch_id', 'status']);
        });
        Schema::create('accounting_opening_balance_reconciliations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_opening_balance_batches')->cascadeOnDelete();
            $t->foreignId('entry_id')->constrained('accounting_opening_balance_entries')->cascadeOnDelete();
            $t->decimal('expected_amount', 20, 2);
            $t->decimal('actual_amount', 20, 2);
            $t->decimal('variance', 20, 2);
            $t->string('status', 24);
            $t->string('external_ref', 190)->nullable();
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['batch_id', 'entry_id']);
        });
        Schema::create('accounting_opening_balance_audits', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_opening_balance_batches')->cascadeOnDelete();
            $t->string('event_type', 80);
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->json('payload');
            $t->char('payload_hash', 64);
            $t->timestamp('created_at');
            $t->index(['batch_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_opening_balance_audits');
        Schema::dropIfExists('accounting_opening_balance_reconciliations');
        Schema::dropIfExists('accounting_opening_balance_entries');
        Schema::dropIfExists('accounting_opening_balance_batches');
    }
};

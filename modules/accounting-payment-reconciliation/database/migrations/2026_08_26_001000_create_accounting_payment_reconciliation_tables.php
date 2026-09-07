<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payment_reconciliation_runs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('provider', 100);
            $t->string('merchant_ref', 190)->nullable();
            $t->string('settlement_ref', 190);
            $t->date('period_start');
            $t->date('period_end');
            $t->char('currency', 3);
            $t->decimal('gross_amount', 20, 2)->default(0);
            $t->decimal('fee_amount', 20, 2)->default(0);
            $t->decimal('refund_amount', 20, 2)->default(0);
            $t->decimal('dispute_amount', 20, 2)->default(0);
            $t->decimal('net_amount', 20, 2)->default(0);
            $t->string('status', 32)->default('imported');
            $t->string('idempotency_key', 190)->nullable();
            $t->char('source_hash', 64);
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'provider', 'settlement_ref']);
            $t->index(['team_id', 'status', 'period_end']);
        });
        Schema::create('accounting_payment_reconciliation_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_payment_reconciliation_runs')->cascadeOnDelete();
            $t->string('external_ref', 190);
            $t->string('type', 24);
            $t->char('currency', 3);
            $t->decimal('gross_amount', 20, 2)->default(0);
            $t->decimal('fee_amount', 20, 2)->default(0);
            $t->decimal('refund_amount', 20, 2)->default(0);
            $t->decimal('dispute_amount', 20, 2)->default(0);
            $t->decimal('net_amount', 20, 2)->default(0);
            $t->string('status', 32)->default('unmatched');
            $t->string('reference_type', 160)->nullable();
            $t->string('reference_id', 190)->nullable();
            $t->json('source_payload');
            $t->char('source_hash', 64);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'external_ref']);
            $t->index(['run_id', 'status', 'type']);
        });
        Schema::create('accounting_payment_reconciliation_matches', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_payment_reconciliation_runs')->cascadeOnDelete();
            $t->foreignId('item_id')->constrained('accounting_payment_reconciliation_items')->cascadeOnDelete();
            $t->string('reference_type', 160);
            $t->string('reference_id', 190);
            $t->decimal('matched_amount', 20, 2);
            $t->decimal('confidence', 5, 4)->default(0);
            $t->string('status', 24)->default('matched');
            $t->unsignedBigInteger('matched_by')->nullable();
            $t->timestamp('matched_at');
            $t->string('idempotency_key', 190)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['item_id', 'reference_type', 'reference_id']);
            $t->index(['run_id', 'status']);
        });
        Schema::create('accounting_payment_reconciliation_exceptions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_payment_reconciliation_runs')->cascadeOnDelete();
            $t->string('kind', 48);
            $t->string('external_ref', 190)->nullable();
            $t->decimal('expected_amount', 20, 2)->nullable();
            $t->decimal('actual_amount', 20, 2)->nullable();
            $t->char('currency', 3)->nullable();
            $t->string('status', 24)->default('open');
            $t->string('severity', 24)->default('blocking');
            $t->text('resolution')->nullable();
            $t->unsignedBigInteger('resolved_by')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['run_id', 'status', 'kind']);
        });
        Schema::create('accounting_payment_reconciliation_drifts', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_payment_reconciliation_runs')->cascadeOnDelete();
            $t->string('field', 100);
            $t->text('expected_value');
            $t->text('actual_value');
            $t->string('severity', 24)->default('warning');
            $t->string('status', 24)->default('open');
            $t->text('resolution')->nullable();
            $t->unsignedBigInteger('resolved_by')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'field']);
        });
        Schema::create('accounting_payment_reconciliation_audits', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_payment_reconciliation_runs')->cascadeOnDelete();
            $t->string('event_type', 80);
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->json('payload');
            $t->char('payload_hash', 64);
            $t->timestamp('created_at');
            $t->index(['run_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payment_reconciliation_audits');
        Schema::dropIfExists('accounting_payment_reconciliation_drifts');
        Schema::dropIfExists('accounting_payment_reconciliation_exceptions');
        Schema::dropIfExists('accounting_payment_reconciliation_matches');
        Schema::dropIfExists('accounting_payment_reconciliation_items');
        Schema::dropIfExists('accounting_payment_reconciliation_runs');
    }
};

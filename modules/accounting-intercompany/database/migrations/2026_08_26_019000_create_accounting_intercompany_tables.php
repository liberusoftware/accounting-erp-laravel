<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_intercompany_counterparties', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('entity_ref');
            $t->string('counterparty_ref');
            $t->string('name');
            $t->char('default_currency', 3);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'entity_ref', 'counterparty_ref']);
        });
        Schema::create('accounting_intercompany_trading_rules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('counterparty_id')->constrained('accounting_intercompany_counterparties')->cascadeOnDelete();
            $t->string('rule_ref');
            $t->string('description');
            $t->string('pricing_method');
            $t->decimal('markup_percent', 12, 4)->default(0);
            $t->char('currency', 3);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_intercompany_transactions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('transaction_ref');
            $t->foreignId('counterparty_id')->constrained('accounting_intercompany_counterparties')->restrictOnDelete();
            $t->string('source_entity_ref');
            $t->string('target_entity_ref');
            $t->string('transaction_type');
            $t->string('description');
            $t->decimal('amount', 18, 2);
            $t->char('currency', 3);
            $t->string('status')->index();
            $t->timestamp('transaction_date');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'transaction_ref']);
        });
        Schema::create('accounting_intercompany_confirmations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained('accounting_intercompany_transactions')->cascadeOnDelete();
            $t->string('entity_ref');
            $t->string('status');
            $t->decimal('confirmed_amount', 18, 2)->nullable();
            $t->text('comment')->nullable();
            $t->string('actor_ref');
            $t->timestamp('confirmed_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_intercompany_settlements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained('accounting_intercompany_transactions')->cascadeOnDelete();
            $t->string('settlement_ref');
            $t->decimal('amount', 18, 2);
            $t->char('currency', 3);
            $t->timestamp('settled_at');
            $t->string('source_ref');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_intercompany_differences', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained('accounting_intercompany_transactions')->cascadeOnDelete();
            $t->string('difference_ref');
            $t->decimal('amount', 18, 2);
            $t->string('reason');
            $t->string('status');
            $t->string('actor_ref');
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_intercompany_transfer_pricing_evidence', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained('accounting_intercompany_transactions')->cascadeOnDelete();
            $t->string('evidence_ref');
            $t->string('kind');
            $t->string('file_ref')->nullable();
            $t->text('description')->nullable();
            $t->decimal('arm_length_value', 18, 2)->nullable();
            $t->char('currency', 3);
            $t->timestamp('captured_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_intercompany_reconciliations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('reconciliation_ref');
            $t->string('period_ref');
            $t->string('entity_ref');
            $t->string('counterparty_ref');
            $t->unsignedInteger('transaction_count');
            $t->decimal('source_total', 18, 2);
            $t->decimal('counterparty_total', 18, 2);
            $t->decimal('difference_total', 18, 2);
            $t->string('status');
            $t->string('actor_ref');
            $t->timestamp('reconciled_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'reconciliation_ref']);
        });
    }

    public function down(): void
    {
        foreach (['reconciliations', 'transfer_pricing_evidence', 'differences', 'settlements', 'confirmations', 'transactions', 'trading_rules', 'counterparties'] as $s) {
            Schema::dropIfExists('accounting_intercompany_'.$s);
        }
    }
};

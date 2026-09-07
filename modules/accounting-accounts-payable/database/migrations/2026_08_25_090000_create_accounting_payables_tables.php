<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ap_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->decimal('current_balance', 20, 2)->default(0);
            $table->boolean('payment_hold')->default(false);
            $table->string('hold_reason')->nullable();
            $table->string('control_account_code', 64)->default('accounts_payable');
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique('party_id');
        });
        Schema::create('accounting_ap_open_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->string('source_type', 160)->nullable();
            $table->string('source_id', 160)->nullable();
            $table->string('reference', 128);
            $table->date('issued_on');
            $table->date('due_on')->nullable();
            $table->decimal('original_amount', 20, 2);
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->char('currency', 3);
            $table->string('payment_terms', 64)->nullable();
            $table->string('status', 16)->default('open');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['source_type', 'source_id']);
            $table->index(['party_id', 'status', 'due_on']);
        });
        Schema::create('accounting_ap_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('accounting_master_parties')->nullOnDelete();
            $table->date('paid_on');
            $table->decimal('amount', 20, 2);
            $table->decimal('applied_amount', 20, 2)->default(0);
            $table->char('currency', 3);
            $table->string('reference', 128)->nullable();
            $table->string('status', 16)->default('unapplied');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['party_id', 'status', 'paid_on']);
        });
        Schema::create('accounting_ap_payment_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('accounting_ap_payments')->cascadeOnDelete();
            $table->foreignId('open_item_id')->constrained('accounting_ap_open_items')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->timestamps();
            $table->unique(['payment_id', 'open_item_id']);
        });
        Schema::create('accounting_ap_disputes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('open_item_id')->constrained('accounting_ap_open_items')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->string('reason', 255);
            $table->string('status', 16)->default('open');
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['accounting_ap_disputes', 'accounting_ap_payment_applications', 'accounting_ap_payments', 'accounting_ap_open_items', 'accounting_ap_accounts'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

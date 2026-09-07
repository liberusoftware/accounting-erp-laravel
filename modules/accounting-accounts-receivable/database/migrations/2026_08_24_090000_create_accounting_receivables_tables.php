<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ar_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->decimal('credit_limit', 20, 2)->default(0);
            $table->decimal('current_balance', 20, 2)->default(0);
            $table->boolean('credit_hold')->default(false);
            $table->string('hold_reason')->nullable();
            $table->string('control_account_code', 64)->default('accounts_receivable');
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique('party_id');
        });
        Schema::create('accounting_ar_open_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->string('source_type', 160)->nullable();
            $table->string('source_id', 160)->nullable();
            $table->string('reference', 128);
            $table->date('issued_on');
            $table->date('due_on')->nullable();
            $table->decimal('original_amount', 20, 2);
            $table->decimal('applied_amount', 20, 2)->default(0);
            $table->char('currency', 3);
            $table->string('status', 16)->default('open');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['source_type', 'source_id']);
            $table->index(['party_id', 'status', 'due_on']);
        });
        Schema::create('accounting_ar_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('accounting_master_parties')->nullOnDelete();
            $table->date('received_on');
            $table->decimal('amount', 20, 2);
            $table->decimal('applied_amount', 20, 2)->default(0);
            $table->char('currency', 3);
            $table->string('reference', 128)->nullable();
            $table->string('status', 16)->default('unapplied');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['party_id', 'status', 'received_on']);
        });
        Schema::create('accounting_ar_receipt_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('receipt_id')->constrained('accounting_ar_receipts')->cascadeOnDelete();
            $table->foreignId('open_item_id')->constrained('accounting_ar_open_items')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->timestamps();
            $table->unique(['receipt_id', 'open_item_id']);
        });
        Schema::create('accounting_ar_disputes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('open_item_id')->constrained('accounting_ar_open_items')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->string('reason', 255);
            $table->string('status', 16)->default('open');
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['status', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ar_disputes');
        Schema::dropIfExists('accounting_ar_receipt_applications');
        Schema::dropIfExists('accounting_ar_receipts');
        Schema::dropIfExists('accounting_ar_open_items');
        Schema::dropIfExists('accounting_ar_accounts');
    }
};

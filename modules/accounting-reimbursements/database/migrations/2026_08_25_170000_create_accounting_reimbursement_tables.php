<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_reimbursement_batches', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->char('currency', 3);
            $t->decimal('total_amount', 20, 2);
            $t->string('status', 24)->default('draft');
            $t->string('provider', 80)->nullable();
            $t->string('provider_ref', 190)->nullable();
            $t->timestamp('exported_at')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_reimbursement_liabilities', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('payee_ref', 190);
            $t->string('source_type', 80)->nullable();
            $t->string('source_id', 190)->nullable();
            $t->string('kind', 24)->default('expense');
            $t->char('currency', 3);
            $t->decimal('amount', 20, 2);
            $t->timestamp('approved_at')->nullable();
            $t->string('status', 24)->default('approved');
            $t->foreignId('batch_id')->nullable()->constrained('accounting_reimbursement_batches')->nullOnDelete();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'payee_ref', 'status']);
            $t->index(['source_type', 'source_id']);
        });
        Schema::create('accounting_reimbursement_remittances', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_reimbursement_batches')->cascadeOnDelete();
            $t->string('payee_ref', 190);
            $t->decimal('amount', 20, 2);
            $t->char('currency', 3);
            $t->string('status', 24);
            $t->string('document_ref', 190)->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['batch_id', 'payee_ref']);
        });
        Schema::create('accounting_reimbursement_reconciliations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_reimbursement_batches')->cascadeOnDelete();
            $t->decimal('expected_amount', 20, 2);
            $t->decimal('settled_amount', 20, 2);
            $t->decimal('variance', 20, 2);
            $t->string('status', 24);
            $t->string('external_ref', 190)->nullable();
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['batch_id', 'external_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_reimbursement_reconciliations');
        Schema::dropIfExists('accounting_reimbursement_remittances');
        Schema::dropIfExists('accounting_reimbursement_liabilities');
        Schema::dropIfExists('accounting_reimbursement_batches');
    }
};

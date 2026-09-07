<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payroll_payment_batches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('batch_ref', 150);
            $table->string('net_pay_ref')->nullable();
            $table->string('liability_ref')->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->decimal('net_pay_amount', 20, 2)->default(0);
            $table->decimal('liability_amount', 20, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->string('provider')->nullable();
            $table->string('provider_payment_ref')->nullable();
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->string('reconciliation_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'batch_ref']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payroll_payment_batches');
    }
};

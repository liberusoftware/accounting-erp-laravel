<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payroll_liabilities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('agency_ref')->nullable();
            $table->string('payee_ref')->nullable();
            $table->string('liability_ref', 150);
            $table->char('currency', 3)->default('GBP');
            $table->decimal('amount', 20, 2);
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->date('due_on')->nullable();
            $table->string('status', 24)->default('open');
            $table->string('payment_ref')->nullable();
            $table->string('allocation_ref')->nullable();
            $table->string('exception_code')->nullable();
            $table->text('exception_message')->nullable();
            $table->string('reconciliation_ref')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'liability_ref']);
            $table->index(['team_id', 'status', 'due_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payroll_liabilities');
    }
};

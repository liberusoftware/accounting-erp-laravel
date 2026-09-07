<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payroll_journals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('journal_ref', 150);
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->char('currency', 3)->default('GBP');
            $table->decimal('gross_pay', 20, 2);
            $table->decimal('taxes', 20, 2)->default(0);
            $table->decimal('deductions', 20, 2)->default(0);
            $table->decimal('benefits', 20, 2)->default(0);
            $table->decimal('employer_costs', 20, 2)->default(0);
            $table->decimal('net_pay', 20, 2);
            $table->json('liabilities')->nullable();
            $table->json('allocation')->nullable();
            $table->string('status', 24)->default('draft');
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->string('reversal_ref')->nullable();
            $table->string('correction_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'journal_ref']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payroll_journals');
    }
};

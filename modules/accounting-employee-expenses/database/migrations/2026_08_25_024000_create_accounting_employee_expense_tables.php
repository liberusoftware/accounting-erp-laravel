<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_employee_expense_claims', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('employee_ref');
            $t->string('claim_ref');
            $t->char('currency', 3);
            $t->string('status')->index();
            $t->date('submitted_on')->nullable();
            $t->date('approved_on')->nullable();
            $t->date('reimbursed_on')->nullable();
            $t->date('posted_on')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->string('project_ref')->nullable();
            $t->json('dimension_ref')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'claim_ref']);
        });
        Schema::create('accounting_employee_expense_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('claim_id')->constrained('accounting_employee_expense_claims')->cascadeOnDelete();
            $t->string('category_ref');
            $t->date('spent_on');
            $t->text('description');
            $t->decimal('amount', 18, 2);
            $t->decimal('tax_amount', 18, 2)->default(0);
            $t->string('merchant')->nullable();
            $t->string('receipt_ref')->nullable();
            $t->decimal('per_diem_days', 8, 2)->nullable();
            $t->json('attendees')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_employee_expense_categories', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('category_ref');
            $t->string('name');
            $t->decimal('daily_limit', 18, 2)->nullable();
            $t->boolean('requires_receipt')->default(false);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'category_ref']);
        });
        Schema::create('accounting_employee_expense_history', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('claim_id')->constrained('accounting_employee_expense_claims')->cascadeOnDelete();
            $t->string('event');
            $t->string('actor_ref')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_employee_expense_history');
        Schema::dropIfExists('accounting_employee_expense_categories');
        Schema::dropIfExists('accounting_employee_expense_items');
        Schema::dropIfExists('accounting_employee_expense_claims');
    }
};

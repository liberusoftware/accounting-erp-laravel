<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_budgets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->date('period_start');
            $table->date('period_end');
            $table->char('currency', 3);
            $table->string('status', 24)->index();
            $table->unsignedInteger('version')->default(1);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name', 'period_start', 'period_end', 'version']);
        });
        Schema::create('accounting_budget_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('budget_id')->constrained('accounting_budgets')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->json('dimensions')->nullable();
            $table->decimal('planned_amount', 20, 2);
            $table->json('phases')->nullable();
            $table->decimal('actual_amount', 20, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_budget_lines');
        Schema::dropIfExists('accounting_budgets');
    }
};

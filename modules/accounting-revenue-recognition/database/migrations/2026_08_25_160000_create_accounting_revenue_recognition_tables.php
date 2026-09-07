<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_revenue_obligations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('source_type')->nullable();
            $t->string('source_id')->nullable();
            $t->string('description')->nullable();
            $t->char('currency', 3);
            $t->decimal('total_amount', 20, 2);
            $t->date('start_date');
            $t->unsignedInteger('periods');
            $t->string('status', 24)->default('active');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
            $t->index(['source_type', 'source_id']);
        });
        Schema::create('accounting_revenue_allocation_references', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('reference_type', 80);
            $t->string('reference_id', 190);
            $t->json('allocation');
            $t->string('status', 24)->default('active');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['reference_type', 'reference_id']);
        });
        Schema::create('accounting_revenue_schedules', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('obligation_id')->constrained('accounting_revenue_obligations')->cascadeOnDelete();
            $t->unsignedBigInteger('allocation_reference_id')->nullable();
            $t->decimal('total_amount', 20, 2);
            $t->date('start_date');
            $t->unsignedInteger('periods');
            $t->string('deferred_account_ref', 190);
            $t->string('revenue_account_ref', 190);
            $t->string('status', 24)->default('active');
            $t->boolean('funded')->default(false);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['status', 'funded']);
        });
        Schema::create('accounting_revenue_schedule_entries', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('schedule_id')->constrained('accounting_revenue_schedules')->cascadeOnDelete();
            $t->unsignedInteger('period_number');
            $t->date('recognition_date');
            $t->decimal('amount', 20, 2);
            $t->string('status', 24)->default('active');
            $t->timestamp('recognized_at')->nullable();
            $t->string('ledger_reference', 190)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['schedule_id', 'period_number']);
            $t->index(['status', 'recognition_date']);
        });
        Schema::create('accounting_revenue_modifications', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('schedule_id')->constrained('accounting_revenue_schedules')->cascadeOnDelete();
            $t->date('effective_date');
            $t->decimal('amount_delta', 20, 2);
            $t->text('reason');
            $t->string('status', 24)->default('pending');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_revenue_recognition_runs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->date('as_of_date');
            $t->string('status', 24)->default('pending');
            $t->unsignedInteger('processed_entries')->default(0);
            $t->unsignedInteger('failed_entries')->default(0);
            $t->json('errors')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('finished_at')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'as_of_date', 'status']);
        });
        Schema::create('accounting_revenue_reconciliations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_revenue_recognition_runs')->cascadeOnDelete();
            $t->string('reference_type', 80);
            $t->string('reference_id', 190);
            $t->decimal('expected_amount', 20, 2);
            $t->decimal('recognized_amount', 20, 2);
            $t->decimal('variance', 20, 2);
            $t->string('status', 24);
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_revenue_reconciliations');
        Schema::dropIfExists('accounting_revenue_recognition_runs');
        Schema::dropIfExists('accounting_revenue_modifications');
        Schema::dropIfExists('accounting_revenue_schedule_entries');
        Schema::dropIfExists('accounting_revenue_schedules');
        Schema::dropIfExists('accounting_revenue_allocation_references');
        Schema::dropIfExists('accounting_revenue_obligations');
    }
};

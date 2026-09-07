<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_project_billings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('project_job_id')->index();
            $table->string('method');
            $table->string('status')->default('draft');
            $table->date('period_start');
            $table->date('period_end');
            $table->char('currency', 3)->default('GBP');
            $table->decimal('quantity', 20, 4)->default(0);
            $table->decimal('rate', 20, 4)->default(0);
            $table->decimal('amount', 20, 2)->default(0);
            $table->decimal('progress_percent', 7, 2)->default(0);
            $table->decimal('billable_time_amount', 20, 2)->default(0);
            $table->decimal('billable_expense_amount', 20, 2)->default(0);
            $table->decimal('retainer_amount', 20, 2)->default(0);
            $table->decimal('write_up_down_amount', 20, 2)->default(0);
            $table->string('source_ref')->nullable();
            $table->string('invoice_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['project_job_id', 'method', 'source_ref', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_project_billings');
    }
};

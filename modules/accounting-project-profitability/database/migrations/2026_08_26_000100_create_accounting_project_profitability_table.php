<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_project_profitability', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('project_job_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->char('currency', 3)->default('GBP');
            foreach (['revenue_amount', 'cost_amount', 'estimate_amount', 'committed_amount', 'actual_amount', 'unbilled_wip_amount', 'billed_amount'] as $field) {
                $table->decimal($field, 20, 2)->default(0);
            }$table->string('status')->default('draft');
            $table->json('dimensions')->nullable();
            $table->json('source_links')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['project_job_id', 'period_start', 'period_end']);
            $table->index(['team_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_project_profitability');
    }
};

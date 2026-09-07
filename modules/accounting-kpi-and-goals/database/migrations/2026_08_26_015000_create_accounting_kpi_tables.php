<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_kpi_metrics', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('metric_ref', 190);
            $t->string('name', 190);
            $t->text('description')->nullable();
            $t->string('unit', 40);
            $t->string('direction', 20)->default('higher');
            $t->string('source_contract', 190);
            $t->text('formula')->nullable();
            $t->string('owner_ref', 190)->nullable();
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'metric_ref']);
        });
        Schema::create('accounting_kpi_goals', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->foreignId('metric_id')->constrained('accounting_kpi_metrics')->cascadeOnDelete();
            $t->string('goal_ref', 190);
            $t->string('name', 190);
            $t->string('owner_ref', 190);
            $t->date('period_start');
            $t->date('period_end');
            $t->decimal('baseline', 20, 6);
            $t->decimal('target', 20, 6);
            $t->decimal('warning_threshold', 20, 6)->nullable();
            $t->decimal('critical_threshold', 20, 6)->nullable();
            $t->string('status', 24)->default('draft');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'goal_ref']);
            $t->index(['team_id', 'status', 'period_end']);
        });
        Schema::create('accounting_kpi_measurements', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->foreignId('metric_id')->constrained('accounting_kpi_metrics')->cascadeOnDelete();
            $t->foreignId('goal_id')->constrained('accounting_kpi_goals')->cascadeOnDelete();
            $t->string('period_ref', 80);
            $t->date('measured_on');
            $t->decimal('value', 20, 6);
            $t->decimal('progress', 12, 6);
            $t->string('source_ref', 190);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['goal_id', 'period_ref']);
        });
        Schema::create('accounting_kpi_alerts', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->foreignId('goal_id')->constrained('accounting_kpi_goals')->cascadeOnDelete();
            $t->foreignId('measurement_id')->constrained('accounting_kpi_measurements')->cascadeOnDelete();
            $t->string('severity', 20);
            $t->string('status', 24)->default('open');
            $t->text('message');
            $t->timestamp('triggered_at');
            $t->string('acknowledged_by', 190)->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'severity']);
        });
        Schema::create('accounting_kpi_commentary', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('goal_id')->constrained('accounting_kpi_goals')->cascadeOnDelete();
            $t->string('actor_ref', 190);
            $t->text('body');
            $t->string('period_ref', 80)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_kpi_commentary');
        Schema::dropIfExists('accounting_kpi_alerts');
        Schema::dropIfExists('accounting_kpi_measurements');
        Schema::dropIfExists('accounting_kpi_goals');
        Schema::dropIfExists('accounting_kpi_metrics');
    }
};

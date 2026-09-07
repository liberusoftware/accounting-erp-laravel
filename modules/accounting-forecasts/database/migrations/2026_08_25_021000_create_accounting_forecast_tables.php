<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_forecasts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('forecast_ref');
            $table->string('name');
            $table->char('currency', 3);
            $table->string('method');
            $table->string('status')->index();
            $table->string('base_period');
            $table->unsignedInteger('horizon_periods');
            $table->string('scenario_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'forecast_ref']);
        });
        Schema::create('accounting_forecast_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('accounting_forecasts')->cascadeOnDelete();
            $table->string('period_ref');
            $table->string('account_ref');
            $table->string('dimension_ref')->nullable();
            $table->string('description');
            $table->string('driver_ref')->nullable();
            $table->decimal('forecast_value', 18, 2);
            $table->decimal('actual_value', 18, 2)->default(0);
            $table->decimal('variance_value', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['forecast_id', 'period_ref', 'account_ref', 'dimension_ref']);
        });
        Schema::create('accounting_forecast_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('accounting_forecasts')->cascadeOnDelete();
            $table->string('period_ref');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status');
            $table->boolean('is_rolling')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_forecast_assumptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('accounting_forecasts')->cascadeOnDelete();
            $table->string('assumption_ref');
            $table->string('name');
            $table->decimal('value', 18, 6);
            $table->string('unit');
            $table->string('source');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_forecast_approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('accounting_forecasts')->cascadeOnDelete();
            $table->string('actor_ref');
            $table->boolean('approved');
            $table->text('comment')->nullable();
            $table->timestamp('decided_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_forecast_actuals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('accounting_forecasts')->cascadeOnDelete();
            $table->foreignId('line_id')->nullable()->constrained('accounting_forecast_lines')->nullOnDelete();
            $table->string('period_ref');
            $table->decimal('actual_value', 18, 2);
            $table->string('source_ref');
            $table->timestamp('replaced_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['actuals', 'approvals', 'assumptions', 'periods', 'lines', 'forecasts'] as $suffix) {
            Schema::dropIfExists('accounting_forecast_'.$suffix);
        }
    }
};

<?php

// 2026_07_07_100002_create_forecast_scenario_lines_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_scenario_lines', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('forecast_scenario_id')->constrained()->cascadeOnDelete();
            $t->string('account_type');
            $t->decimal('adjustment_pct', 8, 2)->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_scenario_lines');
    }
};

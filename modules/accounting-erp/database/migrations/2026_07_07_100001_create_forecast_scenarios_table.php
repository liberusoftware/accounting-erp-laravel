<?php

// 2026_07_07_100001_create_forecast_scenarios_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_scenarios', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_scenarios');
    }
};

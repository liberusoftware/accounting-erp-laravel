<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accounting_forecasts')) {
            return;
        }
        $migration = require __DIR__.'/2026_08_25_021000_create_accounting_forecast_tables.php';
        $migration->up();
    }

    public function down(): void {}
};

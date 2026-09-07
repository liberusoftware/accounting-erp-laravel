<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_cash_flow_forecasts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('forecast_ref', 160);
            $table->string('currency', 3);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->decimal('opening_cash', 20, 8)->default(0);
            $table->decimal('forecast_cash', 20, 8)->default(0);
            $table->decimal('variance', 20, 8)->default(0);
            $table->decimal('confidence', 8, 6)->default(0);
            $table->json('receivables')->nullable();
            $table->json('payables')->nullable();
            $table->json('recurring_items')->nullable();
            $table->json('scenarios')->nullable();
            $table->json('assumptions')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'forecast_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_cash_flow_forecasts');
    }
};

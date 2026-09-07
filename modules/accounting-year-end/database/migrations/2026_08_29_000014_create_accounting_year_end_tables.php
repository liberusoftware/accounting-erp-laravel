<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_year_end_periods', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('period_ref', 160);
            $table->date('period_end');
            $table->string('status', 40)->default('open');
            $table->decimal('retained_earnings', 20, 8)->default(0);
            $table->json('opening_balances')->nullable();
            $table->json('statutory_handoff')->nullable();
            $table->json('evidence')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'period_ref']);
        });
        Schema::create('accounting_year_end_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('period_id')->constrained('accounting_year_end_periods')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('adjustment_ref', 160);
            $table->decimal('amount', 20, 8);
            $table->string('description', 255);
            $table->string('journal_ref', 160)->nullable();
            $table->string('status', 40)->default('draft');
            $table->json('evidence')->nullable();
            $table->timestamps();
            $table->unique(['period_id', 'adjustment_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_year_end_adjustments');
        Schema::dropIfExists('accounting_year_end_periods');
    }
};

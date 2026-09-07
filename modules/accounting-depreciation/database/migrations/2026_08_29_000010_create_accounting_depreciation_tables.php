<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_depreciation_tax_books', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('book_ref');
            $table->string('name');
            $table->string('jurisdiction')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'book_ref']);
        });

        Schema::create('accounting_depreciation_schedules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('asset_ref');
            $table->string('book_ref');
            $table->string('method');
            $table->string('convention')->default('full_month');
            $table->unsignedInteger('useful_life_months');
            $table->decimal('cost', 20, 2);
            $table->decimal('residual_value', 20, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('currency', 3);
            $table->string('status')->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'asset_ref', 'book_ref']);
        });

        Schema::create('accounting_depreciation_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('schedule_id')->constrained('accounting_depreciation_schedules')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 20, 2);
            $table->decimal('accumulated_amount', 20, 2);
            $table->string('status')->default('calculated')->index();
            $table->string('journal_ref')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['schedule_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_depreciation_runs');
        Schema::dropIfExists('accounting_depreciation_schedules');
        Schema::dropIfExists('accounting_depreciation_tax_books');
    }
};

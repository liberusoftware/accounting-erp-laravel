<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_time_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('worker_ref', 160);
            $table->string('customer_ref', 160)->nullable();
            $table->string('project_ref', 160)->nullable();
            $table->string('task_ref', 160)->nullable();
            $table->date('worked_on');
            $table->decimal('hours', 10, 4);
            $table->decimal('billable_rate', 20, 6)->nullable();
            $table->decimal('cost_rate', 20, 6)->nullable();
            $table->char('currency', 3)->nullable();
            $table->boolean('billable')->default(true);
            $table->string('status', 32)->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_time_timers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('worker_ref', 160);
            $table->string('project_ref', 160)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('stopped_at')->nullable();
            $table->string('status', 32)->index();
            $table->timestamps();
        });
        Schema::create('accounting_time_exports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('destination', 32);
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('entry_count')->default(0);
            $table->string('status', 32)->default('pending');
            $table->timestamp('exported_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_time_exports');
        Schema::dropIfExists('accounting_time_timers');
        Schema::dropIfExists('accounting_time_entries');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_workforce_costs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('worker_ref', 160);
            $table->string('source_type', 160)->nullable();
            $table->string('source_id', 160)->nullable();
            $table->date('cost_date');
            $table->decimal('hours', 14, 6)->default(0);
            $table->decimal('hourly_rate', 20, 6)->default(0);
            $table->decimal('amount', 20, 6);
            $table->string('allocation_type', 32)->nullable()->index();
            $table->string('allocation_ref', 160)->nullable();
            $table->boolean('capitalized')->default(false);
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'cost_date']);
        });
        Schema::create('accounting_workforce_costing_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name', 160);
            $table->string('allocation_type', 32);
            $table->string('allocation_ref', 160)->nullable();
            $table->decimal('hourly_rate', 20, 6)->nullable();
            $table->boolean('capitalize')->default(false);
            $table->boolean('active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_workforce_costing_rules');
        Schema::dropIfExists('accounting_workforce_costs');
    }
};

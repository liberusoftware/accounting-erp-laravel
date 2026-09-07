<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_dashboards', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('dashboard_ref', 160);
            $table->string('name', 160);
            $table->string('role', 120)->nullable();
            $table->string('period', 80)->default('current');
            $table->json('dimensions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'dashboard_ref']);
        });
        Schema::create('accounting_dashboard_kpis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dashboard_id')->constrained('accounting_dashboards')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('kpi_ref', 160);
            $table->string('label', 160);
            $table->decimal('value', 20, 8)->default(0);
            $table->decimal('target', 20, 8)->nullable();
            $table->string('unit', 40)->nullable();
            $table->timestamp('refreshed_at')->nullable();
            $table->json('drill_through')->nullable();
            $table->timestamps();
            $table->unique(['dashboard_id', 'kpi_ref']);
        });
        Schema::create('accounting_dashboard_shares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dashboard_id')->constrained('accounting_dashboards')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('shared_with_user_id')->nullable();
            $table->string('shared_with_role', 120)->nullable();
            $table->string('token', 160)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_dashboard_shares');
        Schema::dropIfExists('accounting_dashboard_kpis');
        Schema::dropIfExists('accounting_dashboards');
    }
};

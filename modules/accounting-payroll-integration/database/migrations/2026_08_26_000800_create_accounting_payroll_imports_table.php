<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payroll_imports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('provider', 100);
            $table->date('period_start');
            $table->date('period_end');
            $table->string('run_ref', 150);
            $table->char('currency', 3)->default('GBP');
            $table->json('employee_refs')->nullable();
            $table->json('contractor_refs')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('project_refs')->nullable();
            $table->char('payload_hash', 64);
            $table->json('validation_errors')->nullable();
            $table->string('adapter_ref')->nullable();
            $table->string('status', 24)->default('received');
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'provider', 'run_ref']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payroll_imports');
    }
};

<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_operational_report_runs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('report_key', 120);
            $t->string('name', 190);
            $t->string('category', 40);
            $t->date('period_start');
            $t->date('period_end');
            $t->char('currency', 3)->nullable();
            $t->string('status', 24)->default('ready');
            $t->json('filters')->nullable();
            $t->json('summary')->nullable();
            $t->char('source_hash', 64);
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->unsignedBigInteger('published_by')->nullable();
            $t->timestamp('published_at')->nullable();
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'report_key', 'source_hash']);
            $t->index(['team_id', 'category', 'status', 'period_end']);
        });
        Schema::create('accounting_operational_report_rows', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_operational_report_runs')->cascadeOnDelete();
            $t->string('row_key', 190);
            $t->string('label', 255);
            $t->string('source_type', 160)->nullable();
            $t->string('source_id', 190)->nullable();
            $t->decimal('amount', 20, 2)->default(0);
            $t->char('currency', 3)->nullable();
            $t->string('state', 40)->nullable();
            $t->json('dimensions')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'row_key']);
            $t->index(['run_id', 'state']);
        });
        Schema::create('accounting_operational_report_exceptions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_operational_report_runs')->cascadeOnDelete();
            $t->string('exception_key', 120);
            $t->string('severity', 24)->default('warning');
            $t->text('message');
            $t->string('source_type', 160)->nullable();
            $t->string('source_id', 190)->nullable();
            $t->string('status', 24)->default('open');
            $t->text('resolution')->nullable();
            $t->unsignedBigInteger('resolved_by')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['run_id', 'status', 'severity']);
        });
        Schema::create('accounting_operational_report_audits', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_operational_report_runs')->cascadeOnDelete();
            $t->string('event_type', 80);
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->json('payload');
            $t->char('payload_hash', 64);
            $t->timestamp('created_at');
            $t->index(['run_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_operational_report_audits');
        Schema::dropIfExists('accounting_operational_report_exceptions');
        Schema::dropIfExists('accounting_operational_report_rows');
        Schema::dropIfExists('accounting_operational_report_runs');
    }
};

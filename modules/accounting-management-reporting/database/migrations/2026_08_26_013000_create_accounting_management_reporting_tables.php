<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_management_report_packs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('report_ref', 190);
            $t->string('name', 190);
            $t->date('period_start');
            $t->date('period_end');
            $t->char('currency', 3);
            $t->string('status', 24)->default('draft');
            $t->unsignedInteger('version')->default(1);
            $t->string('owner_ref', 190)->nullable();
            $t->string('approved_by', 190)->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamp('delivered_at')->nullable();
            $t->timestamp('archived_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'report_ref', 'version']);
            $t->index(['team_id', 'status', 'period_end']);
        });
        Schema::create('accounting_management_report_narratives', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('report_pack_id')->constrained('accounting_management_report_packs')->cascadeOnDelete();
            $t->string('section_ref', 100);
            $t->string('title', 190);
            $t->longText('body');
            $t->string('author_ref', 190)->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['report_pack_id', 'section_ref', 'version']);
        });
        Schema::create('accounting_management_report_charts', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('report_pack_id')->constrained('accounting_management_report_packs')->cascadeOnDelete();
            $t->string('chart_ref', 100);
            $t->string('title', 190);
            $t->string('chart_type', 40);
            $t->string('data_source', 190);
            $t->json('series');
            $t->json('options')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['report_pack_id', 'chart_ref']);
        });
        Schema::create('accounting_management_report_schedules', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('report_pack_id')->constrained('accounting_management_report_packs')->cascadeOnDelete();
            $t->string('frequency', 30);
            $t->string('timezone', 80);
            $t->json('recipients');
            $t->timestamp('next_run_at');
            $t->boolean('active')->default(true);
            $t->timestamp('last_run_at')->nullable();
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['active', 'next_run_at']);
        });
        Schema::create('accounting_management_report_reviews', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('report_pack_id')->constrained('accounting_management_report_packs')->cascadeOnDelete();
            $t->string('actor_ref', 190);
            $t->string('decision', 24);
            $t->text('comment')->nullable();
            $t->timestamp('reviewed_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['report_pack_id', 'decision']);
        });
        Schema::create('accounting_management_report_deliveries', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('report_pack_id')->constrained('accounting_management_report_packs')->cascadeOnDelete();
            $t->string('format', 20);
            $t->string('file_ref', 190)->nullable();
            $t->string('status', 24)->default('pending');
            $t->json('recipients');
            $t->string('checksum', 128)->nullable();
            $t->timestamp('delivered_at')->nullable();
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['report_pack_id', 'format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_management_report_deliveries');
        Schema::dropIfExists('accounting_management_report_reviews');
        Schema::dropIfExists('accounting_management_report_schedules');
        Schema::dropIfExists('accounting_management_report_charts');
        Schema::dropIfExists('accounting_management_report_narratives');
        Schema::dropIfExists('accounting_management_report_packs');
    }
};

<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_custom_reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('report_ref', 160);
            $table->string('name', 160);
            $table->json('measures');
            $table->json('dimensions')->nullable();
            $table->json('filters')->nullable();
            $table->json('grouping')->nullable();
            $table->json('formulas')->nullable();
            $table->json('comparisons')->nullable();
            $table->json('layout')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'report_ref']);
        });
        Schema::create('accounting_custom_report_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('report_id')->constrained('accounting_custom_reports')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('variant_ref', 160);
            $table->json('configuration');
            $table->timestamps();
            $table->unique(['report_id', 'variant_ref']);
        });
        Schema::create('accounting_custom_report_exports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('report_id')->constrained('accounting_custom_reports')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('format', 20);
            $table->string('status', 30)->default('requested');
            $table->json('parameters')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_custom_report_exports');
        Schema::dropIfExists('accounting_custom_report_variants');
        Schema::dropIfExists('accounting_custom_reports');
    }
};

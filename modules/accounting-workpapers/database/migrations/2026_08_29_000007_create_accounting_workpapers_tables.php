<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_workpapers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('title', 160);
            $table->string('reference', 80)->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->unsignedBigInteger('preparer_id')->nullable()->index();
            $table->unsignedBigInteger('reviewer_id')->nullable()->index();
            $table->text('conclusion')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_workpaper_references', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->string('label', 160);
            $table->string('target_type', 160)->nullable();
            $table->string('target_id', 160)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_workpaper_procedures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->text('description');
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_workpaper_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('disk', 64)->default('private');
            $table->string('path', 500);
            $table->string('mime_type', 160)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_workpaper_rollovers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->foreignId('target_workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->string('status', 32)->default('completed');
            $table->timestamps();
            $table->unique(['source_workpaper_id', 'target_workpaper_id']);
        });
        Schema::create('accounting_workpaper_exports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workpaper_id')->constrained('accounting_workpapers')->cascadeOnDelete();
            $table->string('format', 32);
            $table->string('status', 32)->default('pending')->index();
            $table->string('path', 500)->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_workpaper_exports');
        Schema::dropIfExists('accounting_workpaper_rollovers');
        Schema::dropIfExists('accounting_workpaper_attachments');
        Schema::dropIfExists('accounting_workpaper_procedures');
        Schema::dropIfExists('accounting_workpaper_references');
        Schema::dropIfExists('accounting_workpapers');
    }
};

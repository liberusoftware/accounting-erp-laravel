<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_migration_sources', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('source_ref', 190);
            $t->string('provider', 80);
            $t->string('source_type', 80);
            $t->string('name', 190);
            $t->unsignedBigInteger('record_count')->default(0);
            $t->string('checksum', 128)->nullable();
            $t->string('status', 24)->default('active');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'source_ref']);
        });
        Schema::create('accounting_migration_mappings', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('source_id')->constrained('accounting_migration_sources')->cascadeOnDelete();
            $t->string('mapping_ref', 190);
            $t->string('entity_type', 100);
            $t->json('field_map');
            $t->json('transforms')->nullable();
            $t->json('validation_rules')->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['source_id', 'mapping_ref', 'version']);
        });
        Schema::create('accounting_migration_batches', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('batch_ref', 190);
            $t->foreignId('source_id')->constrained('accounting_migration_sources')->cascadeOnDelete();
            $t->foreignId('mapping_id')->constrained('accounting_migration_mappings')->restrictOnDelete();
            $t->string('status', 24)->default('draft');
            $t->boolean('dry_run')->default(false);
            $t->string('resume_token', 128)->nullable();
            $t->unsignedBigInteger('total_count')->default(0);
            $t->unsignedBigInteger('processed_count')->default(0);
            $t->unsignedBigInteger('success_count')->default(0);
            $t->unsignedBigInteger('error_count')->default(0);
            $t->unsignedBigInteger('skipped_count')->default(0);
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamp('paused_at')->nullable();
            $t->text('failure_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'batch_ref']);
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_migration_rows', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_migration_batches')->cascadeOnDelete();
            $t->string('row_ref', 190);
            $t->string('source_key', 190);
            $t->string('destination_key', 190)->nullable();
            $t->json('payload');
            $t->json('transformed_payload')->nullable();
            $t->string('status', 24)->default('pending');
            $t->string('error_code', 80)->nullable();
            $t->text('error_message')->nullable();
            $t->unsignedInteger('attempts')->default(0);
            $t->timestamp('processed_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['batch_id', 'row_ref']);
            $t->index(['batch_id', 'status']);
        });
        Schema::create('accounting_migration_attachments', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_migration_batches')->cascadeOnDelete();
            $t->string('file_ref', 190);
            $t->string('name', 190);
            $t->string('mime_type', 120)->nullable();
            $t->unsignedBigInteger('size_bytes')->nullable();
            $t->string('checksum', 128)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['batch_id', 'file_ref']);
        });
        Schema::create('accounting_migration_reconciliations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('batch_id')->constrained('accounting_migration_batches')->cascadeOnDelete();
            $t->unsignedBigInteger('source_count');
            $t->unsignedBigInteger('imported_count');
            $t->unsignedBigInteger('failed_count');
            $t->decimal('source_total', 20, 2)->nullable();
            $t->decimal('destination_total', 20, 2)->nullable();
            $t->decimal('variance', 20, 2)->nullable();
            $t->string('status', 24);
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_migration_reconciliations');
        Schema::dropIfExists('accounting_migration_attachments');
        Schema::dropIfExists('accounting_migration_rows');
        Schema::dropIfExists('accounting_migration_batches');
        Schema::dropIfExists('accounting_migration_mappings');
        Schema::dropIfExists('accounting_migration_sources');
    }
};

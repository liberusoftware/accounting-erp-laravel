<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_qbo_migration_connections', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('realm_id', 190);
            $t->text('access_token')->nullable();
            $t->text('refresh_token')->nullable();
            $t->timestamp('token_expires_at')->nullable();
            $t->string('status', 24)->default('active');
            $t->timestamp('last_synced_at')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'realm_id']);
        });
        Schema::create('accounting_qbo_migration_runs', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('connection_id')->nullable()->constrained('accounting_qbo_migration_connections')->nullOnDelete();
            $t->string('status', 24)->default('draft');
            $t->unsignedInteger('total_records')->default(0);
            $t->unsignedInteger('imported_records')->default(0);
            $t->unsignedInteger('failed_records')->default(0);
            $t->json('errors')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('finished_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at']);
        });
        Schema::create('accounting_qbo_migration_records', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_qbo_migration_runs')->cascadeOnDelete();
            $t->string('entity_type', 80);
            $t->string('source_id', 190);
            $t->string('status', 24)->default('pending');
            $t->json('payload');
            $t->char('payload_hash', 64)->nullable();
            $t->text('error_message')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'entity_type', 'source_id']);
            $t->index(['entity_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_qbo_migration_records');
        Schema::dropIfExists('accounting_qbo_migration_runs');
        Schema::dropIfExists('accounting_qbo_migration_connections');
    }
};

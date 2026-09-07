<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_xero_connections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('tenant_ref', 160);
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'tenant_ref']);
        });
        Schema::create('accounting_xero_migration_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('connection_id')->constrained('accounting_xero_connections')->cascadeOnDelete();
            $table->string('source_type', 64);
            $table->string('source_id', 160);
            $table->string('target_type', 160)->nullable();
            $table->string('target_id', 160)->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->text('error')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->unique(['connection_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_xero_migration_records');
        Schema::dropIfExists('accounting_xero_connections');
    }
};

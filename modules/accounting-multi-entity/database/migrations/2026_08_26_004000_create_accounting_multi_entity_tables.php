<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_multi_entity_books', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('entity_ref', 190);
            $t->string('code', 80);
            $t->string('name', 190);
            $t->char('base_currency', 3);
            $t->string('timezone', 80)->default('UTC');
            $t->string('tax_registration', 190)->nullable();
            $t->string('status', 24)->default('active');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'entity_ref']);
            $t->unique(['team_id', 'code']);
        });
        Schema::create('accounting_multi_entity_access', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('entity_id')->constrained('accounting_multi_entity_books')->cascadeOnDelete();
            $t->string('user_ref', 190);
            $t->string('role', 80);
            $t->json('permissions')->nullable();
            $t->boolean('is_default')->default(false);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['entity_id', 'user_ref']);
            $t->index(['user_ref', 'is_default']);
        });
        Schema::create('accounting_multi_entity_policies', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('entity_id')->constrained('accounting_multi_entity_books')->cascadeOnDelete();
            $t->string('policy_key', 100);
            $t->string('mode', 24);
            $t->json('configuration')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['entity_id', 'policy_key']);
        });
        Schema::create('accounting_multi_entity_periods', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('entity_id')->constrained('accounting_multi_entity_books')->cascadeOnDelete();
            $t->string('period_ref', 100);
            $t->date('starts_on');
            $t->date('ends_on');
            $t->json('tax_configuration')->nullable();
            $t->string('status', 24)->default('open');
            $t->unsignedBigInteger('closed_by')->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['entity_id', 'period_ref']);
            $t->index(['entity_id', 'status', 'starts_on']);
        });
        Schema::create('accounting_multi_entity_mappings', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('entity_id')->constrained('accounting_multi_entity_books')->cascadeOnDelete();
            $t->string('mapping_type', 100);
            $t->string('source_ref', 190);
            $t->string('target_ref', 190);
            $t->string('description', 255)->nullable();
            $t->boolean('is_active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['entity_id', 'mapping_type', 'source_ref']);
        });
        Schema::create('accounting_multi_entity_switches', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('entity_id')->constrained('accounting_multi_entity_books')->cascadeOnDelete();
            $t->string('user_ref', 190);
            $t->string('session_ref', 190);
            $t->timestamp('switched_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['user_ref', 'session_ref', 'switched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_multi_entity_switches');
        Schema::dropIfExists('accounting_multi_entity_mappings');
        Schema::dropIfExists('accounting_multi_entity_periods');
        Schema::dropIfExists('accounting_multi_entity_policies');
        Schema::dropIfExists('accounting_multi_entity_access');
        Schema::dropIfExists('accounting_multi_entity_books');
    }
};

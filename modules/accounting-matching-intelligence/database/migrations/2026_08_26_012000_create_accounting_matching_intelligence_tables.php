<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_matching_suggestions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('suggestion_ref', 190);
            $t->string('source_type', 100);
            $t->string('source_id', 190);
            $t->string('target_type', 100);
            $t->string('target_id', 190);
            $t->string('match_type', 80);
            $t->decimal('confidence', 8, 6);
            $t->decimal('score', 12, 6)->nullable();
            $t->string('status', 24)->default('suggested');
            $t->decimal('automation_threshold', 8, 6)->nullable();
            $t->text('explanation')->nullable();
            $t->string('algorithm_version', 40)->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'suggestion_ref']);
            $t->unique(['team_id', 'source_type', 'source_id', 'target_type', 'target_id', 'match_type']);
            $t->index(['team_id', 'status', 'confidence']);
        });
        Schema::create('accounting_matching_evidence', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('suggestion_id')->constrained('accounting_matching_suggestions')->cascadeOnDelete();
            $t->string('kind', 50);
            $t->string('field', 100)->nullable();
            $t->text('source_value')->nullable();
            $t->text('target_value')->nullable();
            $t->decimal('weight', 8, 6)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['suggestion_id', 'kind']);
        });
        Schema::create('accounting_matching_feedback', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('suggestion_id')->constrained('accounting_matching_suggestions')->cascadeOnDelete();
            $t->string('actor_ref', 190);
            $t->string('feedback_type', 24);
            $t->text('comment')->nullable();
            $t->json('features')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['suggestion_id', 'feedback_type']);
        });
        Schema::create('accounting_matching_thresholds', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('match_type', 80);
            $t->decimal('minimum_confidence', 8, 6);
            $t->decimal('maximum_amount', 20, 2)->nullable();
            $t->boolean('require_evidence')->default(true);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'match_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_matching_thresholds');
        Schema::dropIfExists('accounting_matching_feedback');
        Schema::dropIfExists('accounting_matching_evidence');
        Schema::dropIfExists('accounting_matching_suggestions');
    }
};

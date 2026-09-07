<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_journal_approvals', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('approval_ref', 190);
            $t->string('journal_type', 80);
            $t->string('journal_source', 100);
            $t->string('journal_ref', 190);
            $t->string('preparer_ref', 190);
            $t->string('reviewer_ref', 190)->nullable();
            $t->char('currency', 3);
            $t->decimal('amount', 20, 2);
            $t->decimal('threshold_amount', 20, 2)->nullable();
            $t->string('status', 30)->default('draft');
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('decided_at')->nullable();
            $t->timestamp('posted_at')->nullable();
            $t->text('emergency_reason')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'approval_ref']);
            $t->index(['team_id', 'status', 'journal_type']);
        });
        Schema::create('accounting_journal_decisions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('approval_id')->constrained('accounting_journal_approvals')->cascadeOnDelete();
            $t->string('actor_ref', 190);
            $t->string('decision', 24);
            $t->text('comment')->nullable();
            $t->timestamp('decided_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['approval_id', 'decision']);
        });
        Schema::create('accounting_journal_evidence', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('approval_id')->constrained('accounting_journal_approvals')->cascadeOnDelete();
            $t->string('kind', 50);
            $t->string('file_ref', 190)->nullable();
            $t->text('description')->nullable();
            $t->string('checksum', 128)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_journal_approval_thresholds', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('journal_type', 80);
            $t->decimal('minimum_amount', 20, 2);
            $t->string('reviewer_role', 80);
            $t->unsignedInteger('required_approvals')->default(1);
            $t->boolean('emergency_allowed')->default(false);
            $t->boolean('active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'journal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_approval_thresholds');
        Schema::dropIfExists('accounting_journal_evidence');
        Schema::dropIfExists('accounting_journal_decisions');
        Schema::dropIfExists('accounting_journal_approvals');
    }
};

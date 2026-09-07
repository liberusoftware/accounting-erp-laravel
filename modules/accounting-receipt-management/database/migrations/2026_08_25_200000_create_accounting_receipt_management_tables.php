<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_receipts', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('file_ref', 190);
            $t->string('source_type', 80)->nullable();
            $t->string('source_id', 190)->nullable();
            $t->string('merchant')->nullable();
            $t->decimal('amount', 20, 2)->nullable();
            $t->char('currency', 3)->nullable();
            $t->date('receipt_date')->nullable();
            $t->string('status', 24)->default('inbox');
            $t->date('retention_until')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique('file_ref');
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_receipt_matches', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_receipts')->cascadeOnDelete();
            $t->string('target_type', 80);
            $t->string('target_id', 190);
            $t->decimal('matched_amount', 20, 2)->nullable();
            $t->string('status', 24)->default('confirmed');
            $t->decimal('confidence', 8, 4)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['receipt_id', 'target_type', 'target_id']);
        });
        Schema::create('accounting_missing_receipt_requests', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->foreignId('receipt_id')->nullable()->constrained('accounting_receipts')->nullOnDelete();
            $t->string('requestee_ref', 190);
            $t->string('target_type', 80);
            $t->string('target_id', 190);
            $t->text('reason')->nullable();
            $t->string('status', 24)->default('open');
            $t->date('due_on')->nullable();
            $t->timestamp('fulfilled_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_receipt_annotations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_receipts')->cascadeOnDelete();
            $t->string('author_ref', 190);
            $t->text('body');
            $t->string('visibility', 24)->default('team');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_receipt_audits', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_receipts')->cascadeOnDelete();
            $t->string('action', 80);
            $t->string('actor_ref', 190)->nullable();
            $t->json('evidence')->nullable();
            $t->timestamp('created_at');
            $t->index(['receipt_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_receipt_audits');
        Schema::dropIfExists('accounting_receipt_annotations');
        Schema::dropIfExists('accounting_missing_receipt_requests');
        Schema::dropIfExists('accounting_receipt_matches');
        Schema::dropIfExists('accounting_receipts');
    }
};

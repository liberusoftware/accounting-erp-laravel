<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_recurring_transaction_templates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('name');
            $t->string('transaction_type', 80);
            $t->string('frequency', 24);
            $t->date('starts_on');
            $t->date('next_run_on');
            $t->date('ends_on')->nullable();
            $t->string('status', 24)->default('draft');
            $t->boolean('automatic')->default(false);
            $t->json('date_rules')->nullable();
            $t->json('amount_rules')->nullable();
            $t->json('payload');
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'next_run_on']);
        });
        Schema::create('accounting_recurring_transaction_occurrences', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('template_id')->constrained('accounting_recurring_transaction_templates')->cascadeOnDelete();
            $t->date('occurrence_on');
            $t->string('idempotency_key', 190);
            $t->string('status', 24)->default('draft');
            $t->json('generated_payload')->nullable();
            $t->text('error_message')->nullable();
            $t->timestamp('generated_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['template_id', 'idempotency_key']);
            $t->index(['status', 'occurrence_on']);
        });
        Schema::create('accounting_recurring_transaction_exceptions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('template_id')->constrained('accounting_recurring_transaction_templates')->cascadeOnDelete();
            $t->foreignId('occurrence_id')->nullable()->constrained('accounting_recurring_transaction_occurrences')->nullOnDelete();
            $t->string('kind', 40);
            $t->text('message');
            $t->string('status', 24)->default('open');
            $t->timestamp('resolved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['template_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_recurring_transaction_exceptions');
        Schema::dropIfExists('accounting_recurring_transaction_occurrences');
        Schema::dropIfExists('accounting_recurring_transaction_templates');
    }
};

<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_multi_currency_profiles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('scope_ref', 190);
            $t->char('currency', 3);
            $t->string('role', 24);
            $t->boolean('is_active')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'scope_ref', 'role']);
        });
        Schema::create('accounting_multi_currency_rates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->char('from_currency', 3);
            $t->char('to_currency', 3);
            $t->date('rate_date');
            $t->decimal('rate', 20, 10);
            $t->string('source', 100);
            $t->string('rate_type', 24)->default('spot');
            $t->boolean('is_historical')->default(true);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'from_currency', 'to_currency', 'rate_date', 'rate_type']);
            $t->index(['from_currency', 'to_currency', 'rate_date']);
        });
        Schema::create('accounting_multi_currency_revaluations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('run_ref', 190);
            $t->string('scope_ref', 190)->nullable();
            $t->date('as_of_date');
            $t->char('functional_currency', 3);
            $t->string('status', 24)->default('calculated');
            $t->char('source_hash', 64);
            $t->decimal('realized_gain', 20, 2)->default(0);
            $t->decimal('realized_loss', 20, 2)->default(0);
            $t->decimal('unrealized_gain', 20, 2)->default(0);
            $t->decimal('unrealized_loss', 20, 2)->default(0);
            $t->json('summary')->nullable();
            $t->text('failure_message')->nullable();
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->unsignedBigInteger('posted_by')->nullable();
            $t->timestamp('posted_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'run_ref']);
            $t->index(['team_id', 'status', 'as_of_date']);
        });
        Schema::create('accounting_multi_currency_positions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_multi_currency_revaluations')->cascadeOnDelete();
            $t->string('reference_type', 160);
            $t->string('reference_id', 190);
            $t->char('currency', 3);
            $t->decimal('foreign_amount', 20, 2);
            $t->decimal('book_rate', 20, 10);
            $t->decimal('closing_rate', 20, 10);
            $t->decimal('book_value', 20, 2);
            $t->decimal('closing_value', 20, 2);
            $t->decimal('gain_loss', 20, 2);
            $t->string('gain_status', 24);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'reference_type', 'reference_id']);
            $t->index(['run_id', 'gain_status']);
        });
        Schema::create('accounting_multi_currency_reconciliations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('run_id')->constrained('accounting_multi_currency_revaluations')->cascadeOnDelete();
            $t->string('reference_type', 160);
            $t->string('reference_id', 190);
            $t->decimal('expected_gain_loss', 20, 2);
            $t->decimal('actual_gain_loss', 20, 2);
            $t->decimal('variance', 20, 2);
            $t->string('status', 24);
            $t->string('external_ref', 190)->nullable();
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['run_id', 'reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_multi_currency_reconciliations');
        Schema::dropIfExists('accounting_multi_currency_positions');
        Schema::dropIfExists('accounting_multi_currency_revaluations');
        Schema::dropIfExists('accounting_multi_currency_rates');
        Schema::dropIfExists('accounting_multi_currency_profiles');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_bank_reconciliation_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('bank_account_id')->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('opening_balance', 20, 2);
            $table->decimal('statement_balance', 20, 2);
            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('signed_off_at')->nullable();
            $table->unsignedBigInteger('signed_off_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['bank_account_id', 'period_start', 'period_end']);
        });
        Schema::create('accounting_bank_reconciliation_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->foreignId('session_id')->constrained('accounting_bank_reconciliation_sessions')->cascadeOnDelete();
            $table->string('source_type', 120)->nullable();
            $table->string('source_id', 180)->nullable();
            $table->string('kind', 40);
            $table->string('status', 30)->default('suggested')->index();
            $table->decimal('amount', 20, 2);
            $table->char('currency', 3);
            $table->decimal('confidence', 8, 4)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->text('exception_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamps();
            $table->index(['session_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_bank_reconciliation_entries');
        Schema::dropIfExists('accounting_bank_reconciliation_sessions');
    }
};

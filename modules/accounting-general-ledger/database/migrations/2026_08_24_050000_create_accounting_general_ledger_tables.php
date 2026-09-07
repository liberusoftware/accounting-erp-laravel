<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_journal_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('accounting_books')->cascadeOnDelete();
            $table->string('entry_number', 80);
            $table->date('entry_date');
            $table->string('journal_type', 32)->default('general');
            $table->string('status', 24)->default('draft');
            $table->string('description')->nullable();
            $table->string('source_type')->nullable();
            $table->string('source_id')->nullable();
            $table->foreignId('reversal_of_id')->nullable()->constrained('accounting_journal_entries')->nullOnDelete();
            $table->string('posted_by', 191)->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['book_id', 'entry_number']);
            $table->index(['book_id', 'entry_date', 'status']);
        });
        Schema::create('accounting_journal_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('accounting_journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounting_chart_accounts')->restrictOnDelete();
            $table->decimal('debit', 20, 2)->default(0);
            $table->decimal('credit', 20, 2)->default(0);
            $table->string('description')->nullable();
            $table->json('dimensions')->nullable();
            $table->timestamps();
            $table->index(['account_id', 'journal_entry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_lines');
        Schema::dropIfExists('accounting_journal_entries');
    }
};

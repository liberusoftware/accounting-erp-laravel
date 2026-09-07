<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_recurring_journals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('accounting_books')->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('frequency', 24);
            $table->date('next_run_on');
            $table->date('end_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->json('lines');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['book_id', 'name']);
            $table->index(['is_active', 'next_run_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_recurring_journals');
    }
};

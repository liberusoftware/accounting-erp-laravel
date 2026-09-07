<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_policy_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('accounting_books')->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('key', 100);
            $table->json('value');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('approved_by', 191)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['book_id', 'category', 'key', 'effective_from']);
            $table->index(['book_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_policy_rules');
    }
};

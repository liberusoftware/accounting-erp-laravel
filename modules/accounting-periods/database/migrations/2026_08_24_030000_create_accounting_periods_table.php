<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('accounting_books')->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('state', 24)->default('open');
            $table->date('posting_ends_on')->nullable();
            $table->string('locked_by', 191)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->string('reopened_by', 191)->nullable();
            $table->text('reopen_reason')->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();
            $table->unique(['book_id', 'starts_on', 'ends_on']);
            $table->index(['book_id', 'state', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};

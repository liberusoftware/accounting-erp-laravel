<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_cash_coding_batches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('reference', 180);
            $table->string('status', 30)->default('draft');
            $table->json('lines');
            $table->string('payee_creation_policy', 30)->default('never');
            $table->decimal('total_amount', 20, 8);
            $table->string('currency', 3);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->text('undo_reason')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('undone_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'reference']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_cash_coding_batches');
    }
};

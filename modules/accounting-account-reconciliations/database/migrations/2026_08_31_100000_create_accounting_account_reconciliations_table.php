<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_account_reconciliations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('account_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status', 32)->index();
            $table->json('template')->nullable();
            $table->json('source_balance')->nullable();
            $table->json('supporting_items')->nullable();
            $table->json('preparer')->nullable();
            $table->json('reviewer')->nullable();
            $table->json('aging')->nullable();
            $table->json('certification')->nullable();
            $table->json('carry_forward')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'book_id', 'account_id', 'period_start', 'period_end'], 'account_reconciliation_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_account_reconciliations');
    }
};

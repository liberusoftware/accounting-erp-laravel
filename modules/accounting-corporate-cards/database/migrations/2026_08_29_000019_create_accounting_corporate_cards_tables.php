<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_corporate_card_accounts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('card_ref', 160);
            $table->string('holder_ref', 160);
            $table->string('provider_ref', 160)->nullable();
            $table->string('currency', 3);
            $table->decimal('limit_amount', 20, 8);
            $table->decimal('spent_amount', 20, 8)->default(0);
            $table->string('status', 30)->default('active');
            $table->json('controls')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'card_ref']);
        });
        Schema::create('accounting_corporate_card_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('card_account_id')->constrained('accounting_corporate_card_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('transaction_ref', 160);
            $table->date('transaction_date');
            $table->decimal('amount', 20, 8);
            $table->string('currency', 3);
            $table->string('merchant_ref', 160)->nullable();
            $table->string('status', 30)->default('unassigned');
            $table->string('category_ref', 160)->nullable();
            $table->string('receipt_ref', 160)->nullable();
            $table->string('feed_ref', 160)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('reconciliation_ref', 160)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'transaction_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_corporate_card_transactions');
        Schema::dropIfExists('accounting_corporate_card_accounts');
    }
};

<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_customer_payments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('customer_id', 160);
            $table->string('kind', 40);
            $table->string('reference', 160);
            $table->string('status', 40)->default('unreconciled');
            $table->string('currency', 3);
            $table->decimal('amount', 20, 8);
            $table->decimal('allocated_amount', 20, 8)->default(0);
            $table->decimal('refunded_amount', 20, 8)->default(0);
            $table->string('gateway_reference', 160)->nullable();
            $table->string('deposit_reference', 160)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'kind', 'reference']);
        });
        Schema::create('accounting_customer_payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('accounting_customer_payments')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('document_ref', 160);
            $table->decimal('amount', 20, 8);
            $table->timestamps();
            $table->unique(['payment_id', 'document_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_customer_payment_allocations');
        Schema::dropIfExists('accounting_customer_payments');
    }
};

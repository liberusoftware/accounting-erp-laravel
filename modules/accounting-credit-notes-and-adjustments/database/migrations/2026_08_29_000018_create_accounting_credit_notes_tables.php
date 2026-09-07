<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_credit_notes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('customer_id', 160);
            $table->string('credit_ref', 160);
            $table->string('status', 40)->default('draft');
            $table->string('reason', 160);
            $table->string('currency', 3);
            $table->decimal('amount', 20, 8);
            $table->decimal('allocated_amount', 20, 8)->default(0);
            $table->decimal('tax_amount', 20, 8)->default(0);
            $table->string('refund_reference', 160)->nullable();
            $table->string('store_credit_reference', 160)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('evidence')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'credit_ref']);
        });
        Schema::create('accounting_credit_note_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('credit_note_id')->constrained('accounting_credit_notes')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('invoice_ref', 160);
            $table->decimal('amount', 20, 8);
            $table->timestamps();
            $table->unique(['credit_note_id', 'invoice_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_credit_note_allocations');
        Schema::dropIfExists('accounting_credit_notes');
    }
};

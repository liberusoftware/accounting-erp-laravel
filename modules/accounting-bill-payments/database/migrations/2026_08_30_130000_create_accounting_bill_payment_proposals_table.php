<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_bill_payment_proposals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->string('bill_reference', 180);
            $table->decimal('amount', 20, 2);
            $table->char('currency', 3);
            $table->date('due_date');
            $table->date('discount_date')->nullable();
            $table->decimal('discount_rate', 8, 4)->default(0);
            $table->date('payment_date')->nullable();
            $table->text('bank_details');
            $table->string('provider', 80)->nullable();
            $table->unsignedBigInteger('provider_connection_id')->nullable()->index();
            $table->text('payment_payload')->nullable();
            $table->string('idempotency_key', 180)->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->string('provider_reference')->nullable();
            $table->text('provider_result')->nullable();
            $table->string('remittance_reference')->nullable();
            $table->timestamp('remittance_sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'supplier_id', 'bill_reference']);
            $table->unique(['team_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_bill_payment_proposals');
    }
};

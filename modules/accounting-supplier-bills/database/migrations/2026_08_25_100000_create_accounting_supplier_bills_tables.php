<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_supplier_bills', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('party_id')->constrained('accounting_master_parties')->cascadeOnDelete();
            $table->string('bill_number', 80);
            $table->date('bill_date');
            $table->date('due_on')->nullable();
            $table->string('status', 24)->default('draft');
            $table->string('payment_status', 24)->default('unpaid');
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('tax_total', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('amount_paid', 20, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('capture_source', 64)->nullable();
            $table->string('purchase_order_reference', 128)->nullable();
            $table->string('reference_number', 128)->nullable();
            $table->text('notes')->nullable();
            $table->string('approval_status', 24)->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('recurring')->default(false);
            $table->string('recurrence_frequency', 32)->nullable();
            $table->date('recurrence_start')->nullable();
            $table->date('recurrence_end')->nullable();
            $table->date('last_generated')->nullable();
            $table->json('external_ids')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['party_id', 'bill_number']);
            $table->index(['party_id', 'status', 'due_on']);
            $table->index(['party_id', 'reference_number']);
        });

        Schema::create('accounting_supplier_bill_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bill_id')->constrained('accounting_supplier_bills')->cascadeOnDelete();
            $table->string('account_code', 64)->nullable();
            $table->string('description', 255);
            $table->decimal('quantity', 20, 4)->default(1);
            $table->decimal('unit_price', 20, 4);
            $table->decimal('discount_rate', 8, 4)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('net_amount', 20, 2);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['bill_id', 'account_code']);
        });

        Schema::create('accounting_supplier_bill_credits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bill_id')->constrained('accounting_supplier_bills')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->char('currency', 3);
            $table->string('reason', 255);
            $table->string('reference', 128)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_supplier_bill_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bill_id')->constrained('accounting_supplier_bills')->cascadeOnDelete();
            $table->string('path', 1024);
            $table->string('original_name', 255);
            $table->string('mime_type', 128);
            $table->char('sha256', 64);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['bill_id', 'sha256']);
        });

        Schema::create('accounting_supplier_bill_matches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bill_id')->constrained('accounting_supplier_bills')->cascadeOnDelete();
            $table->string('match_type', 32);
            $table->string('matched_type', 160);
            $table->string('matched_id', 160);
            $table->decimal('quantity', 20, 4)->nullable();
            $table->decimal('amount', 20, 2)->nullable();
            $table->string('status', 24)->default('matched');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['bill_id', 'match_type', 'matched_type', 'matched_id']);
        });
    }

    public function down(): void
    {
        foreach (['accounting_supplier_bill_matches', 'accounting_supplier_bill_documents', 'accounting_supplier_bill_credits', 'accounting_supplier_bill_lines', 'accounting_supplier_bills'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

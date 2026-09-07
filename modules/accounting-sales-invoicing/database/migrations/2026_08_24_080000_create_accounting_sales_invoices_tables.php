<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_sales_invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_number', 80);
            $table->foreignId('party_id')->nullable()->constrained('accounting_master_parties')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_on')->nullable();
            $table->string('status', 24)->default('draft');
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_total', 20, 2)->default(0);
            $table->decimal('tax_total', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->json('branding')->nullable();
            $table->json('recurring_source')->nullable();
            $table->string('delivery_status', 24)->default('not_sent');
            $table->timestamp('delivered_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique('invoice_number');
            $table->index(['party_id', 'status', 'invoice_date']);
        });
        Schema::create('accounting_sales_invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('accounting_sales_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 20, 4);
            $table->decimal('unit_price', 20, 4);
            $table->decimal('discount_rate', 8, 4)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('net_amount', 20, 2);
            $table->decimal('tax_amount', 20, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_sales_invoice_deposits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('accounting_sales_invoices')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->string('currency', 3);
            $table->string('reference', 160)->nullable();
            $table->string('received_by', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sales_invoice_deposits');
        Schema::dropIfExists('accounting_sales_invoice_lines');
        Schema::dropIfExists('accounting_sales_invoices');
    }
};

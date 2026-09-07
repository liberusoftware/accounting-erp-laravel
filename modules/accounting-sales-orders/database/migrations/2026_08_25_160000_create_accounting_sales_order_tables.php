<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_sales_orders', function (Blueprint $t): void {
            $t->id();
            $t->string('customer_id', 160);
            $t->string('estimate_id', 160)->nullable();
            $t->string('order_number', 64);
            $t->date('order_date');
            $t->string('status', 32)->default('draft');
            $t->char('currency', 3);
            $t->decimal('subtotal', 20, 2)->default(0);
            $t->decimal('tax_total', 20, 2)->default(0);
            $t->decimal('total', 20, 2)->default(0);
            $t->decimal('invoiced_total', 20, 2)->default(0);
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique('order_number');
            $t->index(['customer_id', 'status']);
        });
        Schema::create('accounting_sales_order_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('sales_order_id')->constrained('accounting_sales_orders')->cascadeOnDelete();
            $t->string('sku', 100)->nullable();
            $t->text('description');
            $t->decimal('quantity', 20, 4);
            $t->decimal('unit_price', 20, 4);
            $t->decimal('amount', 20, 2);
            $t->decimal('tax_rate', 12, 6)->default(0);
            $t->decimal('tax_amount', 20, 2)->default(0);
            $t->decimal('invoiced_quantity', 20, 4)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_sales_order_deposits', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('sales_order_id')->constrained('accounting_sales_orders')->cascadeOnDelete();
            $t->string('reference', 100);
            $t->decimal('amount', 20, 2);
            $t->char('currency', 3);
            $t->string('status', 24)->default('pending');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['sales_order_id', 'reference']);
        });
        Schema::create('accounting_sales_order_allocations', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('sales_order_id')->constrained('accounting_sales_orders')->cascadeOnDelete();
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('fulfillment_type', 80);
            $t->string('fulfillment_id', 160);
            $t->decimal('quantity', 20, 4);
            $t->string('status', 24)->default('reserved');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['sales_order_id', 'fulfillment_type', 'fulfillment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sales_order_allocations');
        Schema::dropIfExists('accounting_sales_order_deposits');
        Schema::dropIfExists('accounting_sales_order_items');
        Schema::dropIfExists('accounting_sales_orders');
    }
};

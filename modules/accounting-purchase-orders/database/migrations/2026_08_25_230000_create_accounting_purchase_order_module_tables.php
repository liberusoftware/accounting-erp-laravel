<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_purchase_orders_module', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable();
            $t->string('supplier_ref', 190);
            $t->string('order_number', 80);
            $t->char('currency', 3);
            $t->date('order_date');
            $t->date('expected_delivery_on')->nullable();
            $t->decimal('total_amount', 20, 2);
            $t->string('status', 32)->default('draft');
            $t->string('commitment_ref', 190)->nullable();
            $t->string('source_requisition_ref', 190)->nullable();
            $t->text('notes')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique('order_number');
            $t->index(['team_id', 'status']);
        });
        Schema::create('accounting_purchase_order_lines', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained('accounting_purchase_orders_module')->cascadeOnDelete();
            $t->string('item_ref', 190);
            $t->string('description')->nullable();
            $t->decimal('quantity', 20, 4);
            $t->decimal('unit_price', 20, 2);
            $t->decimal('received_quantity', 20, 4)->default(0);
            $t->json('delivery_metadata')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_purchase_order_receipts', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained('accounting_purchase_orders_module')->cascadeOnDelete();
            $t->string('receipt_ref', 190);
            $t->date('received_on');
            $t->json('lines');
            $t->string('status', 24);
            $t->string('document_ref', 190)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['order_id', 'receipt_ref']);
        });
        Schema::create('accounting_purchase_order_changes', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained('accounting_purchase_orders_module')->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->json('changes');
            $t->text('reason');
            $t->string('actor_ref', 190)->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['order_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_purchase_order_changes');
        Schema::dropIfExists('accounting_purchase_order_receipts');
        Schema::dropIfExists('accounting_purchase_order_lines');
        Schema::dropIfExists('accounting_purchase_orders_module');
    }
};

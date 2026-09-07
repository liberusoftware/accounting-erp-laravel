<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_goods_service_receipts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('receipt_ref');
            $t->string('receipt_type');
            $t->string('supplier_ref');
            $t->string('purchase_order_ref')->nullable();
            $t->char('currency', 3);
            $t->string('status')->index();
            $t->timestamp('received_at');
            $t->string('inventory_ref')->nullable();
            $t->string('project_ref')->nullable();
            $t->decimal('total_value', 18, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'receipt_ref']);
        });
        Schema::create('accounting_goods_service_receipt_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_goods_service_receipts')->cascadeOnDelete();
            $t->string('line_ref');
            $t->string('item_ref')->nullable();
            $t->string('description');
            $t->decimal('ordered_quantity', 18, 4)->nullable();
            $t->decimal('received_quantity', 18, 4);
            $t->decimal('returned_quantity', 18, 4)->default(0);
            $t->decimal('unit_price', 18, 4);
            $t->decimal('line_value', 18, 2);
            $t->decimal('variance_quantity', 18, 4)->default(0);
            $t->decimal('variance_value', 18, 2)->default(0);
            $t->string('inventory_ref')->nullable();
            $t->string('project_ref')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_goods_service_confirmations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_goods_service_receipts')->cascadeOnDelete();
            $t->string('confirmation_ref');
            $t->string('service_period');
            $t->decimal('quantity', 18, 4);
            $t->decimal('value', 18, 2);
            $t->string('confirmed_by');
            $t->timestamp('confirmed_at');
            $t->text('comment')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_goods_service_receipt_returns', function (Blueprint $t) {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_goods_service_receipts')->cascadeOnDelete();
            $t->string('return_ref');
            $t->string('line_ref');
            $t->decimal('quantity', 18, 4);
            $t->decimal('value', 18, 2);
            $t->string('reason');
            $t->string('source_ref');
            $t->timestamp('returned_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_goods_service_receipt_attachments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_goods_service_receipts')->cascadeOnDelete();
            $t->string('attachment_ref');
            $t->string('kind');
            $t->string('file_ref');
            $t->text('description')->nullable();
            $t->string('checksum')->nullable();
            $t->string('attached_by');
            $t->timestamp('attached_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_goods_service_receipt_accruals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('receipt_id')->constrained('accounting_goods_service_receipts')->cascadeOnDelete();
            $t->string('accrual_ref');
            $t->decimal('amount', 18, 2);
            $t->char('currency', 3);
            $t->string('period_ref');
            $t->string('status');
            $t->timestamp('posted_at')->nullable();
            $t->string('source_ref');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['accruals', 'attachments', 'returns', 'confirmations', 'lines', 'receipts'] as $s) {
            Schema::dropIfExists($s === 'receipts' ? 'accounting_goods_service_receipts' : 'accounting_goods_service_receipt_'.$s);
        }
    }
};

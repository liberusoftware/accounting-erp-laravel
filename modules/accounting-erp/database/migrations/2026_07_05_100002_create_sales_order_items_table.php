<?php

// 2026_07_05_100002_create_sales_order_items_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $t->foreignId('account_id')->nullable();
            $t->string('description')->nullable();
            $t->integer('quantity')->default(1);
            $t->decimal('unit_price', 15, 2)->default(0);
            $t->decimal('amount', 15, 2)->default(0);
            $t->decimal('tax_amount', 15, 2)->default(0);
            $t->foreignId('tax_rate_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};

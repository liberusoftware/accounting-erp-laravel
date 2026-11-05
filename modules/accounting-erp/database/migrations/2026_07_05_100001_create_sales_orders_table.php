<?php

// 2026_07_05_100001_create_sales_orders_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $t->foreignId('estimate_id')->nullable()->unique()->constrained('estimates', 'estimate_id')->nullOnDelete();
            $t->string('sales_order_number')->unique();
            $t->date('order_date');
            $t->decimal('subtotal_amount', 15, 2)->default(0);
            $t->decimal('tax_amount', 15, 2)->default(0);
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->string('status')->default('draft'); // draft, confirmed, invoiced, cancelled
            $t->text('notes')->nullable();
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};

<?php

// 2026_07_05_100003_add_sales_order_id_to_invoices_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $t): void {
            $t->foreignId('sales_order_id')->nullable()->unique()->after('customer_id')
                ->constrained('sales_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $t): void {
            $t->dropConstrainedForeignId('sales_order_id');
        });
    }
};

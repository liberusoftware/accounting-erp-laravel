<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $t): void {
            $t->foreignId('purchase_request_id')->nullable()->unique()->after('purchase_order_id')
                ->constrained('purchase_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $t): void {
            $t->dropConstrainedForeignId('purchase_request_id');
        });
    }
};

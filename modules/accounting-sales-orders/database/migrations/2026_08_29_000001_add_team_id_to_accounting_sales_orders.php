<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_sales_orders', function (Blueprint $table): void {
            $table->unsignedBigInteger('team_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('accounting_sales_orders', function (Blueprint $table): void {
            $table->dropIndex(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};

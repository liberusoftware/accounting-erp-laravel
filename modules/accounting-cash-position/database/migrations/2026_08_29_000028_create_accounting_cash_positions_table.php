<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_cash_positions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('view_ref', 160);
            $table->string('entity_ref', 160)->nullable();
            $table->string('currency', 3);
            $table->decimal('ledger_balance', 20, 8)->default(0);
            $table->decimal('available_balance', 20, 8)->default(0);
            $table->decimal('outstanding_receipts', 20, 8)->default(0);
            $table->decimal('outstanding_payments', 20, 8)->default(0);
            $table->decimal('committed_cash', 20, 8)->default(0);
            $table->timestamp('refreshed_at');
            $table->timestamps();
            $table->unique(['team_id', 'view_ref', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_cash_positions');
    }
};

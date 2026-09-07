<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_transfers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('source_account_ref', 160);
            $table->string('destination_account_ref', 160);
            $table->char('source_currency', 3);
            $table->char('destination_currency', 3);
            $table->decimal('source_amount', 20, 6);
            $table->decimal('destination_amount', 20, 6);
            $table->decimal('exchange_rate', 20, 10)->default(1);
            $table->decimal('fee_amount', 20, 6)->default(0);
            $table->string('status', 32)->index();
            $table->string('reference', 160)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('accounting_transfer_reconciliations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('transfer_id')->constrained('accounting_transfers')->cascadeOnDelete();
            $table->string('external_ref', 160);
            $table->decimal('amount', 20, 6);
            $table->date('reconciled_on');
            $table->string('status', 32)->default('matched');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'external_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_transfer_reconciliations');
        Schema::dropIfExists('accounting_transfers');
    }
};

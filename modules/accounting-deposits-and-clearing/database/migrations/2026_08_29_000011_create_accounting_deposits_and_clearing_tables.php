<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_clearing_deposits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('deposit_ref');
            $table->string('provider')->nullable();
            $table->string('account_ref');
            $table->string('currency', 3);
            $table->decimal('gross_amount', 20, 2)->default(0);
            $table->decimal('fee_amount', 20, 2)->default(0);
            $table->decimal('payout_amount', 20, 2)->default(0);
            $table->date('deposit_date');
            $table->string('status')->default('open')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'deposit_ref']);
        });

        Schema::create('accounting_clearing_funds', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->foreignId('deposit_id')->nullable()->constrained('accounting_clearing_deposits')->nullOnDelete();
            $table->string('source_type');
            $table->string('source_id');
            $table->decimal('amount', 20, 2);
            $table->string('currency', 3);
            $table->date('received_on');
            $table->string('status')->default('undeposited')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_clearing_funds');
        Schema::dropIfExists('accounting_clearing_deposits');
    }
};

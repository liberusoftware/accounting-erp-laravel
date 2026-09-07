<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_year_end_closes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedInteger('fiscal_year');
            $table->date('period_end');
            $table->string('retained_earnings_account_ref', 160);
            $table->decimal('net_income', 20, 6)->default(0);
            $table->string('status', 32)->index();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->string('closing_entry_ref', 160)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'fiscal_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_year_end_closes');
    }
};

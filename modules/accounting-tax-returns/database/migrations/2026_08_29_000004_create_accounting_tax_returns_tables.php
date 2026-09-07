<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_tax_returns', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('tax_type', 32);
            $table->string('jurisdiction', 64);
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_on')->nullable();
            $table->string('status', 32)->index();
            $table->string('external_reference', 160)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'tax_type', 'jurisdiction', 'period_start', 'period_end']);
        });
        Schema::create('accounting_tax_return_lines', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('tax_return_id')->constrained('accounting_tax_returns')->cascadeOnDelete();
            $table->string('code', 64);
            $table->decimal('amount', 20, 6)->default(0);
            $table->char('currency', 3)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['tax_return_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_tax_return_lines');
        Schema::dropIfExists('accounting_tax_returns');
    }
};

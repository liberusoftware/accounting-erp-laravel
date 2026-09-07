<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_sales_tax_records', function (Blueprint $t): void {
            $t->id();
            $t->string('context_id', 160);
            $t->string('type', 32);
            $t->string('jurisdiction', 64);
            $t->string('origin', 64)->nullable();
            $t->string('destination', 64)->nullable();
            $t->decimal('rate', 12, 6)->default(0);
            $t->decimal('taxable_base', 20, 2)->default(0);
            $t->decimal('liability', 20, 2)->default(0);
            $t->string('status', 24)->default('draft');
            $t->date('period_start');
            $t->date('period_end');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['context_id', 'type', 'jurisdiction', 'period_start']);
            $t->index(['status', 'jurisdiction', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sales_tax_records');
    }
};

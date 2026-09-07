<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_construction_tax_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('subcontractor_ref', 160);
            $table->string('verification_status', 40)->default('pending');
            $table->decimal('deduction_rate', 8, 4)->default(0);
            $table->string('tax_period', 40);
            $table->decimal('gross_amount', 20, 8)->default(0);
            $table->decimal('deduction_amount', 20, 8)->default(0);
            $table->string('return_status', 40)->default('draft');
            $table->string('filing_adapter', 120)->nullable();
            $table->json('verification')->nullable();
            $table->json('statement')->nullable();
            $table->json('correction')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subcontractor_ref', 'tax_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_construction_tax_records');
    }
};

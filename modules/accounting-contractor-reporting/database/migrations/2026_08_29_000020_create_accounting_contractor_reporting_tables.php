<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_contractor_reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('payee_ref', 160);
            $table->string('tax_year', 4);
            $table->string('classification', 80);
            $table->decimal('threshold', 20, 8);
            $table->decimal('reportable_amount', 20, 8)->default(0);
            $table->string('form_type', 40);
            $table->string('status', 40)->default('draft');
            $table->string('filing_adapter', 120)->nullable();
            $table->json('payee_validation')->nullable();
            $table->json('correction')->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'payee_ref', 'tax_year', 'form_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_contractor_reports');
    }
};

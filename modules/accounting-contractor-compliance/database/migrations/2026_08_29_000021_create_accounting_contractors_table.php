<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_contractors', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('contractor_ref', 160);
            $table->string('legal_name', 200);
            $table->string('classification', 80);
            $table->string('status', 40)->default('active');
            $table->string('withholding_scheme', 120)->nullable();
            $table->json('deductions')->nullable();
            $table->json('evidence')->nullable();
            $table->json('statement')->nullable();
            $table->json('regional_export')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'contractor_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_contractors');
    }
};

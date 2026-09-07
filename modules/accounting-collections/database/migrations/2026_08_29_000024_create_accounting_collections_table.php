<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_collection_cases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('case_ref', 160);
            $table->string('customer_ref', 160);
            $table->decimal('balance', 20, 8);
            $table->string('stage', 60)->default('reminder');
            $table->decimal('interest_rate', 8, 4)->default(0);
            $table->json('reminders')->nullable();
            $table->json('statement_run')->nullable();
            $table->json('promise_to_pay')->nullable();
            $table->json('disputes')->nullable();
            $table->json('write_off')->nullable();
            $table->json('agency')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'case_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_collection_cases');
    }
};

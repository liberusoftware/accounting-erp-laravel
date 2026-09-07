<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_automation_recipes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('trigger');
            $table->string('action');
            $table->string('schedule')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('idempotency_key')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_automation_recipes');
    }
};

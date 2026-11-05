<?php

// 2026_07_09_100001_create_plans_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->decimal('amount', 15, 2)->default(0);
            $t->string('currency')->default('USD');
            $t->string('interval')->default('monthly'); // daily, weekly, monthly, yearly
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

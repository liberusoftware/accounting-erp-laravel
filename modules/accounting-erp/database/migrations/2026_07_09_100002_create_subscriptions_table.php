<?php

// 2026_07_09_100002_create_subscriptions_table.php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $t->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $t->string('status')->default('active'); // active, paused, cancelled, expired
            $t->date('started_at')->nullable();
            $t->date('next_billing_date')->nullable();
            $t->date('last_billed_at')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

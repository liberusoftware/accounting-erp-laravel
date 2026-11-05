<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->string('stripe_customer_id')->nullable()->unique();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->string('premium_status')->nullable()->index();
            $table->timestamp('premium_trial_ends_at')->nullable();
            $table->timestamp('premium_current_period_ends_at')->nullable();
            $table->boolean('premium_cancel_at_period_end')->default(false);
            $table->string('premium_last_event_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn([
                'stripe_customer_id', 'stripe_subscription_id', 'stripe_product_id',
                'stripe_monthly_price_id', 'stripe_yearly_price_id', 'stripe_price_id', 'premium_status',
                'premium_trial_ends_at', 'premium_current_period_ends_at',
                'premium_cancel_at_period_end', 'premium_last_event_id',
            ]);
        });
    }
};

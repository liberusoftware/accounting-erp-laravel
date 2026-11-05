<?php

// src/tests/Feature/Subscription/SubscriptionModelTest.php
declare(strict_types=1);

namespace Tests\Feature\Subscription;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Plan;
use Liberu\Accounting\Erp\Models\Subscription;
use Tests\TestCase;

class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_links_customer_and_plan_and_can_be_cancelled(): void
    {
        $customer = Customer::factory()->create();
        $plan = Plan::create(['name' => 'Pro', 'amount' => 50, 'interval' => 'monthly']);
        $sub = Subscription::create([
            'customer_id' => $customer->id, 'plan_id' => $plan->id,
            'status' => 'active', 'started_at' => '2026-06-01', 'next_billing_date' => '2026-07-01',
        ]);

        $this->assertTrue($sub->plan->is($plan));
        $this->assertTrue($sub->customer->is($customer));

        $sub->cancel();
        $this->assertSame('cancelled', $sub->fresh()->status);
    }
}

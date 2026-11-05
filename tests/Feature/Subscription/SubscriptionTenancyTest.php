<?php

// src/tests/Feature/Subscription/SubscriptionTenancyTest.php
declare(strict_types=1);

namespace Tests\Feature\Subscription;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Plan;
use Liberu\Accounting\Erp\Models\Subscription;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class SubscriptionTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_stamps_actor_team(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $plan = Plan::create(['name' => 'Pro', 'amount' => 10, 'interval' => 'monthly']);

        $this->assertSame($team->id, (int) $plan->team_id);
    }

    public function test_subscription_stamps_actor_team(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $plan = Plan::create(['name' => 'Pro', 'amount' => 10, 'interval' => 'monthly']);

        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'next_billing_date' => now()->addMonth(),
        ]);

        $this->assertSame($team->id, (int) $subscription->team_id);
    }
}

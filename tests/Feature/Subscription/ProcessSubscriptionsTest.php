<?php

// src/tests/Feature/Subscription/ProcessSubscriptionsTest.php
declare(strict_types=1);

namespace Tests\Feature\Subscription;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Plan;
use Liberu\Accounting\Erp\Models\Subscription;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class ProcessSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_bills_all_active_due_subscriptions(): void
    {
        Carbon::setTestNow('2026-06-15');
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $plan = Plan::create(['name' => 'Pro', 'amount' => 20, 'interval' => 'monthly', 'team_id' => $team->id]);
        Subscription::create([
            'customer_id' => $customer->id, 'plan_id' => $plan->id, 'status' => 'active',
            'started_at' => '2026-05-15', 'next_billing_date' => '2026-05-15', 'team_id' => $team->id,
        ]);

        $this->artisan('subscriptions:process')->assertSuccessful();

        // Cycles 05-15 and 06-15 due → 2 invoices.
        $this->assertSame(2, Invoice::where('customer_id', $customer->id)->count());
        Carbon::setTestNow();
    }
}

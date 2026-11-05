<?php

// src/tests/Feature/SalesOrder/SalesOrderTenancyTest.php
declare(strict_types=1);

namespace Tests\Feature\SalesOrder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Models\EstimateItem;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\SalesOrderService;
use Tests\TestCase;

class SalesOrderTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_converted_sales_order_carries_actor_team(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);

        $customer = Customer::factory()->create();
        $estimate = Estimate::create([
            'customer_id' => $customer->id, 'estimate_number' => 'EST-9',
            'estimate_date' => '2026-06-01', 'subtotal_amount' => 10,
            'tax_amount' => 0, 'total_amount' => 10, 'status' => 'accepted',
        ]);
        EstimateItem::create(['estimate_id' => $estimate->estimate_id, 'description' => 'x',
            'quantity' => 1, 'unit_price' => 10, 'amount' => 10, 'tax_amount' => 0]);

        $so = app(SalesOrderService::class)->createFromEstimate($estimate);

        $this->assertSame($team->id, (int) $so->team_id);
    }
}

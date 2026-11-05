<?php

// src/tests/Feature/SalesOrder/SalesOrderModelTest.php
declare(strict_types=1);

namespace Tests\Feature\SalesOrder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\SalesOrder;
use Liberu\Accounting\Erp\Models\SalesOrderItem;
use Tests\TestCase;

class SalesOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_order_persists_with_items_and_generates_number(): void
    {
        $so = SalesOrder::create([
            'order_date' => '2026-07-01', 'status' => 'draft',
            'subtotal_amount' => 100, 'tax_amount' => 0, 'total_amount' => 100,
        ]);
        SalesOrderItem::create([
            'sales_order_id' => $so->id, 'description' => 'Widget',
            'quantity' => 1, 'unit_price' => 100, 'amount' => 100, 'tax_amount' => 0,
        ]);

        $this->assertNotEmpty($so->sales_order_number);          // auto-generated
        $this->assertCount(1, $so->fresh()->items);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\InventoryAccounting\Actions\ApplyLandedCost;
use Liberu\Accounting\InventoryAccounting\Actions\CreateInventoryItem;
use Liberu\Accounting\InventoryAccounting\Actions\IssueInventory;
use Liberu\Accounting\InventoryAccounting\Actions\ReceiveInventory;
use Liberu\Accounting\InventoryAccounting\Actions\WriteDownInventory;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Queries\InventoryQuery;
use Tests\TestCase;

final class AccountingInventoryAccountingTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_fifo_issue_landed_cost_and_write_down_update_valuation(): void
    {
        $item = app(CreateInventoryItem::class)->handle(['item_ref' => 'SKU-1', 'description' => 'Widget', 'warehouse_ref' => 'MAIN', 'currency' => 'gbp', 'valuation_method' => 'fifo']);
        app(ReceiveInventory::class)->handle($item, ['movement_ref' => 'GRN-1', 'quantity' => 10, 'unit_cost' => 12, 'source_ref' => 'PO-1']);
        app(ReceiveInventory::class)->handle($item, ['movement_ref' => 'GRN-2', 'quantity' => 5, 'unit_cost' => 15, 'source_ref' => 'PO-2']);
        $cost = app(IssueInventory::class)->handle($item->refresh(), ['movement_ref' => 'SO-1', 'quantity' => 12, 'source_ref' => 'INV-1']);
        app(ApplyLandedCost::class)->handle($item->refresh(), ['cost_ref' => 'LC-1', 'amount' => 30, 'allocation_basis' => 'quantity', 'source_ref' => 'FREIGHT-1']);
        app(WriteDownInventory::class)->handle($item->refresh(), ['write_down_ref' => 'WD-1', 'amount' => 5, 'reason' => 'Damaged packaging']);

        $this->assertSame(150.0, $cost);
        $valuation = app(InventoryQuery::class)->valuation($item->refresh());
        $this->assertSame(3.0, $valuation['quantity_on_hand']);
        $this->assertSame(70.0, $valuation['inventory_value']);
    }

    public function test_issue_cannot_exceed_quantity_on_hand(): void
    {
        $item = app(CreateInventoryItem::class)->handle(['item_ref' => 'SKU-2', 'description' => 'Widget', 'warehouse_ref' => 'MAIN', 'currency' => 'USD', 'valuation_method' => 'weighted_average']);
        $this->expectException(InvalidInventory::class);
        app(IssueInventory::class)->handle($item, ['movement_ref' => 'SO-2', 'quantity' => 1, 'source_ref' => 'INV-2']);
    }
}

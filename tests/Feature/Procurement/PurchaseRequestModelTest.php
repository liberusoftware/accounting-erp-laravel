<?php

declare(strict_types=1);

namespace Tests\Feature\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\PurchaseRequest;
use Liberu\Accounting\Erp\Models\PurchaseRequestItem;
use Tests\TestCase;

class PurchaseRequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_persists_with_items_and_generates_number(): void
    {
        $request = PurchaseRequest::create([
            'request_date' => '2026-07-01', 'status' => 'draft', 'total_amount' => 100,
        ]);
        PurchaseRequestItem::create([
            'purchase_request_id' => $request->id, 'description' => 'Widget',
            'quantity' => 1, 'unit_price' => 100, 'total_price' => 100,
        ]);

        $this->assertNotEmpty($request->request_number);
        $this->assertCount(1, $request->fresh()->items);
        $this->assertSame(100.0, $request->approvalAmount());
    }
}

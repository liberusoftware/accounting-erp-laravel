<?php

// src/tests/Feature/SalesOrder/CreateFromEstimateTest.php
declare(strict_types=1);

namespace Tests\Feature\SalesOrder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Estimate;
use Liberu\Accounting\Erp\Models\EstimateItem;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Services\SalesOrderService;
use Tests\TestCase;

class CreateFromEstimateTest extends TestCase
{
    use RefreshDatabase;

    private function acceptedEstimate(): Estimate
    {
        $customer = Customer::factory()->create();
        $estimate = Estimate::create([
            'customer_id' => $customer->id, 'estimate_number' => 'EST-1',
            'estimate_date' => '2026-06-01', 'subtotal_amount' => 200,
            'tax_amount' => 20, 'total_amount' => 220, 'status' => 'accepted',
        ]);
        EstimateItem::create([
            'estimate_id' => $estimate->estimate_id, 'description' => 'Consulting',
            'quantity' => 2, 'unit_price' => 100, 'amount' => 200, 'tax_amount' => 20,
        ]);

        return $estimate;
    }

    public function test_accepted_estimate_converts_to_confirmed_sales_order(): void
    {
        $estimate = $this->acceptedEstimate();
        $so = app(SalesOrderService::class)->createFromEstimate($estimate);

        $this->assertSame('confirmed', $so->status);
        $this->assertSame($estimate->estimate_id, $so->estimate_id);
        $this->assertSame($estimate->customer_id, $so->customer_id);
        $this->assertSame('220.00', (string) $so->total_amount);
        $this->assertCount(1, $so->items);
        $this->assertSame('Consulting', $so->items->first()->description);
    }

    public function test_non_accepted_estimate_is_rejected(): void
    {
        $estimate = $this->acceptedEstimate();
        $estimate->update(['status' => 'sent']);

        $this->expectException(\DomainException::class);
        app(SalesOrderService::class)->createFromEstimate($estimate);
    }

    public function test_estimate_cannot_convert_twice(): void
    {
        $estimate = $this->acceptedEstimate();
        app(SalesOrderService::class)->createFromEstimate($estimate);

        $this->expectException(\DomainException::class);
        app(SalesOrderService::class)->createFromEstimate($estimate->fresh());
    }

    public function test_estimate_already_invoiced_directly_cannot_become_a_sales_order(): void
    {
        $estimate = $this->acceptedEstimate();
        // Simulate the legacy direct estimate->invoice path having already run.
        $invoice = Invoice::factory()->create();
        $estimate->update(['invoice_id' => $invoice->id]);

        $this->expectException(\DomainException::class);
        app(SalesOrderService::class)->createFromEstimate($estimate->fresh());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Vendor;
use Liberu\Accounting\ErpFilament\Filament\Customer\Resources\Invoices\PortalInvoiceResource;
use Liberu\Accounting\ErpFilament\Filament\Customer\Widgets\PortalBalanceWidget;
use Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\Bills\PortalBillResource;
use Tests\TestCase;

class PortalResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_sees_only_their_own_invoices(): void
    {
        $a = Customer::factory()->create();
        $b = Customer::factory()->create();
        $mine = Invoice::factory()->create(['customer_id' => $a->id]);
        $theirs = Invoice::factory()->create(['customer_id' => $b->id]);

        $this->actingAs($a, 'customer');
        $ids = PortalInvoiceResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($theirs->id, $ids);
    }

    public function test_vendor_sees_only_their_own_bills(): void
    {
        $a = Vendor::factory()->create();
        $b = Vendor::factory()->create();
        $mine = Bill::factory()->create(['vendor_id' => $a->vendor_id]);
        $theirs = Bill::factory()->create(['vendor_id' => $b->vendor_id]);

        $this->actingAs($a, 'vendor');
        $ids = PortalBillResource::getEloquentQuery()->pluck('bill_id')->all();

        $this->assertContains($mine->bill_id, $ids);
        $this->assertNotContains($theirs->bill_id, $ids);
    }

    public function test_portal_resources_are_read_only(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertFalse(PortalInvoiceResource::canCreate());
        $this->assertFalse(PortalInvoiceResource::canEdit($invoice));
        $this->assertFalse(PortalInvoiceResource::canDelete($invoice));
        $this->assertFalse(PortalBillResource::canCreate());
    }

    public function test_customer_balance_widget_sums_only_unpaid_own_invoices(): void
    {
        $a = Customer::factory()->create();
        $b = Customer::factory()->create();
        Invoice::factory()->create(['customer_id' => $a->id, 'payment_status' => 'pending', 'total_amount' => 100]);
        Invoice::factory()->create(['customer_id' => $a->id, 'payment_status' => 'pending', 'total_amount' => 50]);
        Invoice::factory()->create(['customer_id' => $a->id, 'payment_status' => 'paid', 'total_amount' => 999]);
        Invoice::factory()->create(['customer_id' => $b->id, 'payment_status' => 'pending', 'total_amount' => 777]);

        $this->actingAs($a, 'customer');

        $method = new \ReflectionMethod(PortalBalanceWidget::class, 'getStats');
        $method->setAccessible(true);
        $stats = $method->invoke(new PortalBalanceWidget());

        // First stat = outstanding balance: own unpaid only (100 + 50), never 999 (paid) or 777 (other customer).
        $this->assertSame('150.00', $stats[0]->getValue());
    }
}

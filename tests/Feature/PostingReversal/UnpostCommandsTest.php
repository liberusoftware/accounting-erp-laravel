<?php

declare(strict_types=1);

namespace Tests\Feature\PostingReversal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\InvoicePostingService;
use Liberu\Accounting\Erp\Services\PaymentPostingService;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Tests\TestCase;

class UnpostCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_commands_unpost_invoice_and_payment(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 300]);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 300, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);
        app(PaymentPostingService::class)->post($payment);

        // Unpost the payment first (invoice can't be unposted while a posted payment exists).
        $this->artisan('payments:unpost', ['payment' => $payment->payment_id])->assertSuccessful();
        $this->assertNull($payment->fresh()->journal_entry_id);

        app(InvoicePostingService::class)->post($invoice->fresh());
        $this->artisan('invoices:unpost', ['invoice' => $invoice->id])->assertSuccessful();
        $this->assertNull($invoice->fresh()->journal_entry_id);
    }

    public function test_unknown_ids_fail(): void
    {
        $this->artisan('invoices:unpost', ['invoice' => 999999])->assertFailed();
        $this->artisan('payments:unpost', ['payment' => 999999])->assertFailed();
    }
}

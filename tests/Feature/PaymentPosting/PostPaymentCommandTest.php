<?php

declare(strict_types=1);

namespace Tests\Feature\PaymentPosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Tests\TestCase;

class PostPaymentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_posts_the_payment_by_id(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 400]);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 400, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);

        $this->artisan('payments:post', ['payment' => $payment->payment_id])->assertSuccessful();

        $this->assertNotNull($payment->fresh()->journal_entry_id);
        $this->assertSame(1, JournalEntry::where('team_id', $team->id)->count());
    }

    public function test_command_fails_for_unknown_payment(): void
    {
        $this->artisan('payments:post', ['payment' => 999999])->assertFailed();
    }
}

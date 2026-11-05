<?php

declare(strict_types=1);

namespace Tests\Feature\PostingReversal;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\InvoicePostingService;
use Liberu\Accounting\Erp\Services\PaymentPostingService;
use Liberu\Accounting\Erp\Services\PostingReversalService;
use Liberu\Accounting\Erp\Services\RevenueRecognitionService;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use RuntimeException;
use Tests\TestCase;

class ReverseInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0:Team,1:Invoice} */
    private function postedInvoice(float $total = 500): array
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'invoice_date' => '2026-06-01', 'total_amount' => $total]);
        app(InvoicePostingService::class)->post($invoice);

        return [$team, $invoice->fresh()];
    }

    private function account(Team $team, int $number): Account
    {
        return Account::where('team_id', $team->id)->where('account_number', $number)->firstOrFail();
    }

    public function test_reverse_undoes_balances_and_clears_link(): void
    {
        [$team, $invoice] = $this->postedInvoice(500);
        // after posting: AR 1100 = +500, Sales 4000 = +500
        $this->assertSame('500.00', number_format((float) $this->account($team, 1100)->balance, 2, '.', ''));

        app(PostingReversalService::class)->reverseInvoice($invoice);

        $this->assertSame('0.00', number_format((float) $this->account($team, 1100)->balance, 2, '.', ''));
        $this->assertSame('0.00', number_format((float) $this->account($team, 4000)->balance, 2, '.', ''));
        $this->assertNull($invoice->fresh()->journal_entry_id);
        // re-postable
        $entry = app(InvoicePostingService::class)->post($invoice->fresh());
        $this->assertNotNull($entry->id);
    }

    public function test_throws_when_not_posted(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 100]);

        $this->expectException(RuntimeException::class);
        app(PostingReversalService::class)->reverseInvoice($invoice);
    }

    public function test_blocked_when_a_posted_payment_exists(): void
    {
        [$team, $invoice] = $this->postedInvoice(500);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 500, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);
        app(PaymentPostingService::class)->post($payment);

        $this->expectException(RuntimeException::class);
        app(PostingReversalService::class)->reverseInvoice($invoice->fresh());
    }

    public function test_blocked_when_revenue_recognized(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        // RevenueRecognitionService::createFromInvoice rejects an already-posted invoice
        // (see RecognizeDueTest), so the schedule must be created before posting.
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'invoice_date' => '2026-01-01', 'total_amount' => 1200]);
        $deferred = $this->account($team, 2400);
        $sales = $this->account($team, 4000);
        $schedule = app(RevenueRecognitionService::class)->createFromInvoice($invoice, 12, $deferred, $sales);
        app(InvoicePostingService::class)->post($invoice->fresh());

        Carbon::setTestNow('2027-06-01'); // all 12 periods due
        app(RevenueRecognitionService::class)->recognizeDue($schedule->fresh());
        Carbon::setTestNow();

        $this->expectException(RuntimeException::class);
        app(PostingReversalService::class)->reverseInvoice($invoice->fresh());
    }

    public function test_reverse_allowed_when_schedule_has_no_recognized_entries(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Sched', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'invoice_date' => '2026-06-01', 'total_amount' => 1200]);
        $deferred = $this->account($team, 2400);
        $sales = $this->account($team, 4000);
        // Schedule (0 recognized) then post -> Dr AR / Cr Deferred.
        app(RevenueRecognitionService::class)->createFromInvoice($invoice, 12, $deferred, $sales);
        app(InvoicePostingService::class)->post($invoice->fresh());
        $this->assertSame('1200.00', number_format((float) $deferred->fresh()->balance, 2, '.', ''));

        // Allowed — nothing recognized yet.
        app(PostingReversalService::class)->reverseInvoice($invoice->fresh());

        $this->assertNull($invoice->fresh()->journal_entry_id);
        $this->assertSame('0.00', number_format((float) $deferred->fresh()->balance, 2, '.', ''));
    }
}

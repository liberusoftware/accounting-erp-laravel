<?php

declare(strict_types=1);

namespace Tests\Feature\PaymentPosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\PaymentPostingService;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use RuntimeException;
use Tests\TestCase;

class PostPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0:Team,1:Invoice} */
    private function provisioned(float $invoiceTotal = 500): array
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'invoice_date' => '2026-06-01', 'total_amount' => $invoiceTotal, 'payment_status' => 'pending']);

        return [$team, $invoice];
    }

    private function payment(Team $team, Invoice $invoice, float $amount): Payment
    {
        return Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => $amount, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);
    }

    private function account(Team $team, int $number): Account
    {
        return Account::where('team_id', $team->id)->where('account_number', $number)->firstOrFail();
    }

    public function test_posts_dr_cash_cr_ar_and_marks_invoice_paid(): void
    {
        [$team, $invoice] = $this->provisioned(500);
        $payment = $this->payment($team, $invoice, 500);

        $entry = app(PaymentPostingService::class)->post($payment);

        $this->assertTrue($entry->is_posted);
        $this->assertTrue($entry->isBalanced());
        $this->assertSame($team->id, (int) $entry->team_id);
        $this->assertSame($team->user_id, (int) $entry->user_id);
        $this->assertSame(2, $entry->lines()->count());
        // Cash (1000, asset) debited 500; AR (1100, asset) credited 500
        $this->assertSame('500.00', number_format((float) $this->account($team, 1000)->balance, 2, '.', ''));
        $this->assertSame('-500.00', number_format((float) $this->account($team, 1100)->balance, 2, '.', ''));
        $this->assertSame($entry->id, $payment->fresh()->journal_entry_id);
        // invoice fully paid
        $this->assertSame('paid', $invoice->fresh()->payment_status);
    }

    public function test_partial_payment_marks_invoice_partial(): void
    {
        [$team, $invoice] = $this->provisioned(500);
        $payment = $this->payment($team, $invoice, 200);

        app(PaymentPostingService::class)->post($payment);

        $this->assertSame('partial', $invoice->fresh()->payment_status);
    }

    public function test_is_idempotent(): void
    {
        [$team, $invoice] = $this->provisioned(300);
        $payment = $this->payment($team, $invoice, 300);

        $first = app(PaymentPostingService::class)->post($payment);
        $second = app(PaymentPostingService::class)->post($payment->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, JournalEntry::where('team_id', $team->id)->count());
    }

    public function test_overpayment_posts_full_amount_and_marks_paid(): void
    {
        [$team, $invoice] = $this->provisioned(100);
        $payment = $this->payment($team, $invoice, 150);

        app(PaymentPostingService::class)->post($payment);

        $this->assertSame('150.00', number_format((float) $this->account($team, 1000)->balance, 2, '.', ''));
        $this->assertSame('-150.00', number_format((float) $this->account($team, 1100)->balance, 2, '.', ''));
        $this->assertSame('paid', $invoice->fresh()->payment_status);
    }

    public function test_throws_when_chart_not_provisioned(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Bare', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 100]);
        $payment = $this->payment($team, $invoice, 100);

        $this->expectException(RuntimeException::class);
        app(PaymentPostingService::class)->post($payment);
    }

    public function test_status_counts_only_posted_payments(): void
    {
        [$team, $invoice] = $this->provisioned(500);
        // A merely-recorded (unposted) payment for the remainder — must NOT count.
        $this->payment($team, $invoice, 300);
        $posted = $this->payment($team, $invoice, 200);

        app(PaymentPostingService::class)->post($posted);

        // Only the posted 200 counts against the 500 total -> partial, not paid.
        $this->assertSame('partial', $invoice->fresh()->payment_status);
    }
}

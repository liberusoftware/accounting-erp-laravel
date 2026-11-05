<?php

declare(strict_types=1);

namespace Tests\Feature\PostingReversal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Customer;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\Payment;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\PaymentPostingService;
use Liberu\Accounting\Erp\Services\PostingReversalService;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use RuntimeException;
use Tests\TestCase;

class ReversePaymentTest extends TestCase
{
    use RefreshDatabase;

    private function account(Team $team, int $number): Account
    {
        return Account::where('team_id', $team->id)->where('account_number', $number)->firstOrFail();
    }

    public function test_reverse_payment_undoes_balances_clears_link_and_resets_status(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);
        $customer = Customer::factory()->create(['team_id' => $team->id]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'customer_id' => $customer->id, 'total_amount' => 500, 'payment_status' => 'pending']);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 500, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);
        app(PaymentPostingService::class)->post($payment);
        $this->assertSame('paid', $invoice->fresh()->payment_status);

        app(PostingReversalService::class)->reversePayment($payment->fresh());

        $this->assertSame('0.00', number_format((float) $this->account($team, 1000)->balance, 2, '.', '')); // Cash back to 0
        $this->assertSame('0.00', number_format((float) $this->account($team, 1100)->balance, 2, '.', '')); // AR back to 0
        $this->assertNull($payment->fresh()->journal_entry_id);
        $this->assertSame('pending', $invoice->fresh()->payment_status);
    }

    public function test_throws_when_payment_not_posted(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $invoice = Invoice::factory()->create(['team_id' => $team->id, 'total_amount' => 100]);
        $payment = Payment::create(['invoice_id' => $invoice->id, 'payment_amount' => 100, 'payment_date' => '2026-06-05', 'team_id' => $team->id]);

        $this->expectException(RuntimeException::class);
        app(PostingReversalService::class)->reversePayment($payment);
    }
}

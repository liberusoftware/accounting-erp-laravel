<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Liberu\Accounting\AccountsPayable\Actions\ApplyPayment;
use Liberu\Accounting\AccountsPayable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsPayable\Actions\OpenDispute;
use Liberu\Accounting\AccountsPayable\Actions\RecordPayment;
use Liberu\Accounting\AccountsPayable\Actions\ResolveDispute;
use Liberu\Accounting\AccountsPayable\Actions\SetPaymentControl;
use Liberu\Accounting\AccountsPayable\Enums\DisputeStatus;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\AccountsPayable\Events\DisputeOpened;
use Liberu\Accounting\AccountsPayable\Events\DisputeResolved;
use Liberu\Accounting\AccountsPayable\Events\PaymentApplied;
use Liberu\Accounting\AccountsPayable\Events\PaymentRecorded;
use Liberu\Accounting\AccountsPayable\Exceptions\InvalidPayable;
use Liberu\Accounting\AccountsPayable\Queries\AgingQuery;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Tests\TestCase;

final class AccountingAccountsPayableTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_subledger_payment_application_and_aging_are_reconciled(): void
    {
        Event::fake([PaymentRecorded::class, PaymentApplied::class]);
        $supplier = $this->supplier();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $supplier->id, 'reference' => 'BILL-AP-1', 'issued_on' => '2026-08-01', 'due_on' => '2026-08-10', 'original_amount' => 250, 'currency' => 'USD']);
        $payment = app(RecordPayment::class)->handle(['party_id' => $supplier->id, 'paid_on' => '2026-08-05', 'amount' => 100, 'currency' => 'USD']);

        Event::assertDispatched(PaymentRecorded::class);
        app(ApplyPayment::class)->handle($payment, $item, 100);

        $this->assertSame(PayableStatus::Partial, $item->fresh()->status);
        $this->assertSame(PayableStatus::Applied, $payment->fresh()->status);
        Event::assertDispatched(PaymentApplied::class);
        $this->assertDatabaseHas('accounting_ap_accounts', ['party_id' => $supplier->id, 'current_balance' => 150]);
        $this->assertSame(['current' => 0.0, '1_30' => 150.0, '31_60' => 0.0, '61_90' => 0.0, 'over_90' => 0.0], app(AgingQuery::class)->handle($supplier->id, new \DateTimeImmutable('2026-08-15')));
    }

    public function test_payable_currency_invariant_is_enforced(): void
    {
        $supplier = $this->supplier();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $supplier->id, 'reference' => 'BILL-AP-2', 'issued_on' => '2026-08-01', 'original_amount' => 100, 'currency' => 'USD']);
        $payment = app(RecordPayment::class)->handle(['party_id' => $supplier->id, 'amount' => 25, 'currency' => 'EUR']);

        $this->expectException(InvalidPayable::class);
        app(ApplyPayment::class)->handle($payment, $item, 10);
    }

    public function test_dispute_lifecycle_is_enforced_and_emits_events(): void
    {
        Event::fake([DisputeOpened::class, DisputeResolved::class]);
        $supplier = $this->supplier();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $supplier->id, 'reference' => 'BILL-AP-3', 'issued_on' => '2026-08-01', 'original_amount' => 100, 'currency' => 'USD']);

        $dispute = app(OpenDispute::class)->handle($item, 'Price under review', 20);
        $this->assertSame(PayableStatus::Disputed, $item->fresh()->status);
        $this->assertSame(DisputeStatus::Open, $dispute->status);
        Event::assertDispatched(DisputeOpened::class);

        app(ResolveDispute::class)->handle($dispute, 'Approved credit', true);
        $this->assertSame(DisputeStatus::Resolved, $dispute->fresh()->status);
        $this->assertSame(PayableStatus::Open, $item->fresh()->status);
        Event::assertDispatched(DisputeResolved::class);
    }

    public function test_duplicate_open_dispute_is_rejected(): void
    {
        $supplier = $this->supplier();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $supplier->id, 'reference' => 'BILL-AP-4', 'issued_on' => '2026-08-01', 'original_amount' => 100, 'currency' => 'USD']);
        app(OpenDispute::class)->handle($item, 'Price under review', 20);

        $this->expectException(InvalidPayable::class);
        app(OpenDispute::class)->handle($item, 'Price under review', 20);
    }

    public function test_payment_hold_blocks_application_and_dates_are_validated(): void
    {
        $supplier = $this->supplier();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $supplier->id, 'reference' => 'BILL-AP-5', 'issued_on' => '2026-08-01', 'due_on' => '2026-08-31', 'original_amount' => 100, 'currency' => 'usd']);
        $payment = app(RecordPayment::class)->handle(['party_id' => $supplier->id, 'amount' => 25, 'currency' => 'usd']);

        $this->assertSame('USD', $item->currency);
        $this->assertSame('USD', $payment->currency);
        app(SetPaymentControl::class)->handle($supplier->id, true, 'Compliance review');

        $this->expectException(InvalidPayable::class);
        app(ApplyPayment::class)->handle($payment, $item, 10);
    }

    private function supplier(): Party
    {
        $entityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'AP Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);

        return Party::create(['legal_entity_id' => $entityId, 'type' => PartyType::Supplier, 'name' => 'AP Supplier']);
    }
}

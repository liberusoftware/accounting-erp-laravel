<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Liberu\Accounting\AccountsReceivable\Actions\ApplyReceipt;
use Liberu\Accounting\AccountsReceivable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsReceivable\Actions\OpenDispute;
use Liberu\Accounting\AccountsReceivable\Actions\RecordReceipt;
use Liberu\Accounting\AccountsReceivable\Actions\ResolveDispute;
use Liberu\Accounting\AccountsReceivable\Enums\DisputeStatus;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\AccountsReceivable\Events\DisputeOpened;
use Liberu\Accounting\AccountsReceivable\Events\DisputeResolved;
use Liberu\Accounting\AccountsReceivable\Events\ReceiptRecorded;
use Liberu\Accounting\AccountsReceivable\Exceptions\InvalidReceivable;
use Liberu\Accounting\AccountsReceivable\Queries\AgingQuery;
use Liberu\Accounting\AccountsReceivable\Queries\StatementQuery;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Tests\TestCase;

final class AccountingAccountsReceivableTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_subledger_receipts_and_credit_balance_are_reconciled(): void
    {
        $party = $this->party();
        $item = app(CreateOpenItem::class)->handle([
            'party_id' => $party->id,
            'reference' => 'INV-AR-1',
            'issued_on' => '2026-08-01',
            'due_on' => '2026-08-10',
            'original_amount' => 250,
            'currency' => 'USD',
        ]);
        $receipt = app(RecordReceipt::class)->handle([
            'party_id' => $party->id,
            'received_on' => '2026-08-05',
            'amount' => 100,
            'currency' => 'USD',
            'reference' => 'PAY-AR-1',
        ]);

        app(ApplyReceipt::class)->handle($receipt, $item, 100);

        $this->assertSame(ReceivableStatus::Partial, $item->fresh()->status);
        $this->assertSame(ReceivableStatus::Applied, $receipt->fresh()->status);
        $this->assertDatabaseHas('accounting_ar_accounts', ['party_id' => $party->id, 'current_balance' => 150]);
        $this->assertSame(['current' => 0.0, '1_30' => 150.0, '31_60' => 0.0, '61_90' => 0.0, 'over_90' => 0.0], app(AgingQuery::class)->handle($party->id, new \DateTimeImmutable('2026-08-15')));
    }

    public function test_statement_period_has_an_opening_balance_and_disputes_can_be_resolved(): void
    {
        $party = $this->party();
        $old = app(CreateOpenItem::class)->handle(['party_id' => $party->id, 'reference' => 'OLD', 'issued_on' => '2026-07-01', 'original_amount' => 80, 'currency' => 'USD']);
        $current = app(CreateOpenItem::class)->handle(['party_id' => $party->id, 'reference' => 'CURRENT', 'issued_on' => '2026-08-01', 'original_amount' => 120, 'currency' => 'USD']);
        $dispute = app(OpenDispute::class)->handle($current, 'Quantity under review', 20);

        $statement = app(StatementQuery::class)->handle($party->id, new \DateTimeImmutable('2026-08-01'));

        $this->assertSame(80.0, $statement['opening_balance']);
        $this->assertSame(120.0, $statement['charges']);
        $this->assertSame(200.0, $statement['closing_balance']);
        $this->assertSame(DisputeStatus::Open, $dispute->status);
        app(ResolveDispute::class)->handle($dispute, 'Accepted and credited', true);
        $this->assertSame(DisputeStatus::Resolved, $dispute->fresh()->status);
        $this->assertSame(ReceivableStatus::Open, $current->fresh()->status);
        $this->assertNotNull($old->fresh());
    }

    public function test_receivable_invariants_and_after_commit_events_are_enforced(): void
    {
        Event::fake([ReceiptRecorded::class, DisputeOpened::class, DisputeResolved::class]);
        $party = $this->party();
        $item = app(CreateOpenItem::class)->handle(['party_id' => $party->id, 'reference' => 'INVARIANT', 'issued_on' => '2026-08-01', 'original_amount' => 100, 'currency' => 'USD']);
        $receipt = app(RecordReceipt::class)->handle(['party_id' => $party->id, 'amount' => 40, 'currency' => 'USD']);
        Event::assertDispatched(ReceiptRecorded::class);

        app(ApplyReceipt::class)->handle($receipt, $item, 40);
        $this->expectException(InvalidReceivable::class);
        app(ApplyReceipt::class)->handle($receipt, $item, 1);
    }

    private function party(): Party
    {
        $legalEntityId = DB::table('accounting_legal_entities')->insertGetId([
            'name' => 'AR Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Party::create(['legal_entity_id' => $legalEntityId, 'type' => PartyType::Customer, 'name' => 'AR Customer']);
    }
}

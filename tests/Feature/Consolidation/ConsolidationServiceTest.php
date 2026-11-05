<?php

declare(strict_types=1);

namespace Tests\Feature\Consolidation;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\ConsolidationService;
use Liberu\Accounting\Erp\Services\FinancialStatementService;
use Tests\TestCase;

class ConsolidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Carbon $start;

    private Carbon $end;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->start = Carbon::parse('2026-01-01');
        $this->end = Carbon::parse('2026-12-31');
    }

    private function team(): Team
    {
        return Team::forceCreate(['user_id' => $this->user->id, 'name' => 'T', 'personal_team' => false]);
    }

    private function account(int $teamId, string $type): Account
    {
        return Account::factory()->create(['team_id' => $teamId, 'account_type' => $type]);
    }

    /**
     * @param  array<int, array{0:int,1:float,2:float}>  $lines  [accountId, debit, credit]
     */
    private function postEntry(int $teamId, ?int $counterpartyId, array $lines): void
    {
        $entry = JournalEntry::create([
            'team_id' => $teamId,
            'user_id' => $this->user->id,
            'entry_date' => '2026-06-15',
            'counterparty_team_id' => $counterpartyId,
            'is_posted' => true,
        ]);

        foreach ($lines as [$accountId, $debit, $credit]) {
            $entry->lines()->create(['account_id' => $accountId, 'debit_amount' => $debit, 'credit_amount' => $credit]);
        }
    }

    private function group(Team ...$members): ConsolidationGroup
    {
        $group = ConsolidationGroup::create(['name' => 'Group', 'owner_team_id' => $members[0]->id]);
        $group->members()->syncWithoutDetaching(collect($members)->pluck('id')->all());

        return $group;
    }

    public function test_profit_and_loss_aggregates_and_eliminates_intercompany(): void
    {
        $a = $this->team();
        $b = $this->team();
        $aRev = $this->account($a->id, 'revenue');
        $aAr = $this->account($a->id, 'asset');
        $bExp = $this->account($b->id, 'expense');
        $bAp = $this->account($b->id, 'liability');

        // External sale on A: revenue 200 (no counterparty).
        $this->postEntry($a->id, null, [[$aAr->id, 200, 0], [$aRev->id, 0, 200]]);
        // Intercompany sale A -> B: A revenue 100 (counterparty B) ...
        $this->postEntry($a->id, $b->id, [[$aAr->id, 100, 0], [$aRev->id, 0, 100]]);
        // ... and B expense 100 (counterparty A).
        $this->postEntry($b->id, $a->id, [[$bExp->id, 100, 0], [$bAp->id, 0, 100]]);

        $result = app(ConsolidationService::class)->consolidatedProfitAndLoss($this->group($a, $b), $this->start, $this->end);

        // Combined double-counts the intercompany sale; consolidated nets it out.
        $this->assertSame(300.0, $result['combined']['revenue']);
        $this->assertSame(100.0, $result['eliminations']['revenue']);
        $this->assertSame(100.0, $result['eliminations']['expenses']);
        $this->assertSame(200.0, $result['consolidated']['revenue']);
        $this->assertSame(0.0, $result['consolidated']['expenses']);
        $this->assertSame(200.0, $result['consolidated']['net_income']);
    }

    public function test_balance_sheet_eliminates_intercompany_receivable_and_payable(): void
    {
        $a = $this->team();
        $b = $this->team();
        $aAr = $this->account($a->id, 'asset');
        $aRev = $this->account($a->id, 'revenue');
        $bExp = $this->account($b->id, 'expense');
        $bAp = $this->account($b->id, 'liability');

        $this->postEntry($a->id, null, [[$aAr->id, 200, 0], [$aRev->id, 0, 200]]);
        $this->postEntry($a->id, $b->id, [[$aAr->id, 100, 0], [$aRev->id, 0, 100]]);
        $this->postEntry($b->id, $a->id, [[$bExp->id, 100, 0], [$bAp->id, 0, 100]]);

        $result = app(ConsolidationService::class)->consolidatedBalanceSheet($this->group($a, $b), $this->end);

        $this->assertSame(300.0, $result['combined']['assets']);
        $this->assertSame(100.0, $result['eliminations']['assets']);
        $this->assertSame(100.0, $result['eliminations']['liabilities']);
        $this->assertSame(200.0, $result['consolidated']['assets']);
        $this->assertSame(0.0, $result['consolidated']['liabilities']);
    }

    public function test_cash_flow_consolidates_by_simple_sum_without_eliminations(): void
    {
        $a = $this->team();
        $b = $this->team();

        $result = app(ConsolidationService::class)->consolidatedCashFlow($this->group($a, $b), $this->start, $this->end);

        $this->assertSame(0.0, $result['eliminations']['operating']);
        $this->assertSame($result['combined'], $result['consolidated']);
        $this->assertCount(2, $result['members']);
    }

    public function test_with_team_scopes_regardless_of_auth(): void
    {
        $x = $this->team();
        $rev = $this->account($x->id, 'revenue');
        $ar = $this->account($x->id, 'asset');
        $this->postEntry($x->id, null, [[$ar->id, 150, 0], [$rev->id, 0, 150]]);

        // No actingAs: withTeam() must still resolve team x's statement.
        $pl = app(FinancialStatementService::class)->withTeam($x->id)->profitAndLoss($this->start, $this->end);
        $this->assertSame(150.0, (float) $pl['revenue']['total']);

        // Default (no override, no auth) → sentinel team -1 → empty.
        $empty = app(FinancialStatementService::class)->profitAndLoss($this->start, $this->end);
        $this->assertSame(0.0, (float) $empty['revenue']['total']);
    }

    public function test_empty_group_yields_zeros(): void
    {
        $owner = $this->team();
        $group = ConsolidationGroup::create(['name' => 'Empty', 'owner_team_id' => $owner->id]);

        $result = app(ConsolidationService::class)->consolidatedProfitAndLoss($group, $this->start, $this->end);

        $this->assertSame(0.0, $result['consolidated']['net_income']);
        $this->assertSame([], $result['members']);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Carbon\Carbon;
use Liberu\Accounting\Erp\Models\ConsolidationGroup;
use Liberu\Accounting\Erp\Models\JournalEntryLine;

/**
 * Group-level financial statements: aggregate each member team's statement,
 * then net out intercompany amounts (P&L + balance sheet) so a group doesn't
 * double-count sales/loans between its own members.
 *
 * Cash flow consolidates by simple sum this increment (no cash-flow-specific
 * eliminations — see the design spec).
 *
 * CONSTRAINT (until the deferred intercompany-entry authoring UI lands): an
 * intercompany entry is assumed to carry a single in-scope receivable/payable
 * line per side (A/R on the seller, A/P on the buyer). Elimination sums every
 * tagged line of the eliminable types, so a cash-SETTLED or multi-line
 * intercompany entry would over-eliminate. Cash/bank is therefore deliberately
 * excluded from eliminable assets below — an intercompany balance is a
 * receivable/payable, never cash — which defuses the settlement footgun.
 */
class ConsolidationService
{
    /**
     * Asset types eligible for intercompany elimination — the balance-sheet
     * asset types minus cash/bank. Intercompany balances are receivables, not
     * cash, so a cash settlement line on a tagged entry must not be swept into
     * eliminations. (Combined asset totals come from FinancialStatementService,
     * which keeps its own full asset-type list.)
     */
    private const ELIMINABLE_ASSET_TYPES = ['asset', 'current_asset', 'fixed_asset', 'other_asset'];

    /** Balance-sheet liability account types. */
    private const LIABILITY_TYPES = ['liability', 'current_liability', 'long_term_liability'];

    /** Account types whose normal balance is a debit (mirror getAccountBalance). */
    private const DEBIT_NORMAL = ['asset', 'expense', 'bank', 'current_asset', 'fixed_asset', 'other_asset', 'cost_of_goods_sold'];

    /**
     * @return array<string, mixed>
     */
    public function consolidatedProfitAndLoss(ConsolidationGroup $group, Carbon $start, Carbon $end): array
    {
        $memberIds = $this->memberIds($group);
        $members = [];
        $combined = ['revenue' => 0.0, 'cost_of_goods_sold' => 0.0, 'gross_profit' => 0.0, 'expenses' => 0.0, 'net_income' => 0.0];

        foreach ($memberIds as $id) {
            $pl = $this->statements()->withTeam($id)->profitAndLoss($start, $end);
            $members[] = ['team_id' => $id, 'statement' => $pl];
            $combined['revenue'] += (float) $pl['revenue']['total'];
            $combined['cost_of_goods_sold'] += (float) $pl['cost_of_goods_sold']['total'];
            $combined['expenses'] += (float) $pl['expenses']['total'];
            $combined['gross_profit'] += (float) $pl['gross_profit'];
            $combined['net_income'] += (float) $pl['net_income'];
        }

        $eliminations = [
            'revenue' => $this->intercompanyBalance($memberIds, ['revenue'], $start, $end),
            'cost_of_goods_sold' => $this->intercompanyBalance($memberIds, ['cost_of_goods_sold'], $start, $end),
            'expenses' => $this->intercompanyBalance($memberIds, ['expense'], $start, $end),
        ];

        $revenue = $combined['revenue'] - $eliminations['revenue'];
        $cogs = $combined['cost_of_goods_sold'] - $eliminations['cost_of_goods_sold'];
        $expenses = $combined['expenses'] - $eliminations['expenses'];
        $grossProfit = $revenue - $cogs;

        return [
            'period' => ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()],
            'members' => $members,
            'combined' => $combined,
            'eliminations' => $eliminations,
            'consolidated' => [
                'revenue' => $revenue,
                'cost_of_goods_sold' => $cogs,
                'gross_profit' => $grossProfit,
                'expenses' => $expenses,
                'net_income' => $grossProfit - $expenses,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function consolidatedBalanceSheet(ConsolidationGroup $group, Carbon $asOf): array
    {
        $memberIds = $this->memberIds($group);
        $members = [];
        $combined = ['assets' => 0.0, 'liabilities' => 0.0, 'equity' => 0.0];

        foreach ($memberIds as $id) {
            $bs = $this->statements()->withTeam($id)->balanceSheet($asOf);
            $members[] = ['team_id' => $id, 'statement' => $bs];
            $combined['assets'] += (float) $bs['assets']['total'];
            $combined['liabilities'] += (float) $bs['liabilities']['total'];
            $combined['equity'] += (float) $bs['equity']['total'];
        }

        $eliminations = [
            'assets' => $this->intercompanyBalance($memberIds, self::ELIMINABLE_ASSET_TYPES, null, $asOf),
            'liabilities' => $this->intercompanyBalance($memberIds, self::LIABILITY_TYPES, null, $asOf),
        ];

        $assets = $combined['assets'] - $eliminations['assets'];
        $liabilities = $combined['liabilities'] - $eliminations['liabilities'];
        $equity = $combined['equity'];

        return [
            'as_of_date' => $asOf->toDateString(),
            'members' => $members,
            'combined' => $combined,
            'eliminations' => $eliminations,
            'consolidated' => [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'total_liabilities_and_equity' => $liabilities + $equity,
            ],
        ];
    }

    /**
     * Cash flow: simple sum across members, no eliminations this increment.
     *
     * @return array<string, mixed>
     */
    public function consolidatedCashFlow(ConsolidationGroup $group, Carbon $start, Carbon $end): array
    {
        $memberIds = $this->memberIds($group);
        $members = [];
        $combined = ['operating' => 0.0, 'investing' => 0.0, 'financing' => 0.0, 'net_change_in_cash' => 0.0, 'beginning_cash' => 0.0, 'ending_cash' => 0.0];

        foreach ($memberIds as $id) {
            $cf = $this->statements()->withTeam($id)->cashFlowStatement($start, $end);
            $members[] = ['team_id' => $id, 'statement' => $cf];
            $combined['operating'] += (float) $cf['operating_activities']['net_cash_from_operations'];
            $combined['investing'] += (float) $cf['investing_activities']['net_cash_from_investing'];
            $combined['financing'] += (float) $cf['financing_activities']['net_cash_from_financing'];
            $combined['net_change_in_cash'] += (float) $cf['net_change_in_cash'];
            $combined['beginning_cash'] += (float) $cf['beginning_cash'];
            $combined['ending_cash'] += (float) $cf['ending_cash'];
        }

        return [
            'period' => ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()],
            'members' => $members,
            'combined' => $combined,
            'eliminations' => ['operating' => 0.0, 'investing' => 0.0, 'financing' => 0.0],
            'consolidated' => $combined,
        ];
    }

    /**
     * Signed, normal-balance sum of posted intercompany lines: lines on a member
     * account of one of $accountTypes whose entry is tagged with a fellow-member
     * counterparty. This is exactly the portion of the combined totals that is
     * internal to the group, so subtracting it removes the double-count.
     *
     * @param  array<int, int>  $memberIds
     * @param  array<int, string>  $accountTypes
     */
    private function intercompanyBalance(array $memberIds, array $accountTypes, ?Carbon $start, Carbon $end): float
    {
        if ($memberIds === []) {
            return 0.0;
        }

        $lines = JournalEntryLine::query()
            ->whereHas('journalEntry', function ($q) use ($memberIds, $start, $end): void {
                $q->where('is_posted', true)
                    ->whereIn('counterparty_team_id', $memberIds)
                    ->where('entry_date', '<=', $end);
                if ($start instanceof Carbon) {
                    $q->where('entry_date', '>=', $start);
                }
            })
            ->whereHas('account', fn ($q) => $q->whereIn('team_id', $memberIds)->whereIn('account_type', $accountTypes))
            ->with('account')
            ->get();

        $total = 0.0;
        foreach ($lines as $line) {
            $debitNormal = in_array($line->account->account_type, self::DEBIT_NORMAL, true);
            $total += $debitNormal
                ? (float) $line->debit_amount - (float) $line->credit_amount
                : (float) $line->credit_amount - (float) $line->debit_amount;
        }

        return $total;
    }

    /**
     * @return array<int, int>
     */
    private function memberIds(ConsolidationGroup $group): array
    {
        return $group->members()->pluck('teams.id')->map(fn ($v): int => (int) $v)->all();
    }

    private function statements(): FinancialStatementService
    {
        return app(FinancialStatementService::class);
    }
}

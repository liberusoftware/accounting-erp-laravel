<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatements\Queries;

use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialStatements\Exceptions\InvalidStatementRequest;

final class StatementQuery
{
    public function profitAndLoss(int $bookId, string $start, string $end, ?array $dimensions = null): array
    {
        $this->validatePeriod($bookId, $start, $end);
        $rows = $this->accounts($bookId, $end, $start, ['revenue', 'expense', 'cost_of_goods_sold'], $dimensions);
        $revenue = array_values(array_filter($rows, static fn (array $row): bool => $row['type'] === 'revenue'));
        $cogs = array_values(array_filter($rows, static fn (array $row): bool => $row['type'] === 'cost_of_goods_sold'));
        $expenses = array_values(array_filter($rows, static fn (array $row): bool => $row['type'] === 'expense'));
        $sum = static fn (array $set): float => round(array_sum(array_column($set, 'balance')), 2);
        $totalRevenue = $sum($revenue);
        $totalCogs = abs($sum($cogs));
        $totalExpenses = abs($sum($expenses));

        return [
            'type' => 'profit_and_loss',
            'period' => ['start_date' => $start, 'end_date' => $end],
            'dimensions' => $dimensions ?? [],
            'revenue' => ['accounts' => $revenue, 'total' => $totalRevenue],
            'cost_of_goods_sold' => ['accounts' => $cogs, 'total' => $totalCogs],
            'gross_profit' => round($totalRevenue - $totalCogs, 2),
            'expenses' => ['accounts' => $expenses, 'total' => $totalExpenses],
            'net_income' => round($totalRevenue - $totalCogs - $totalExpenses, 2),
        ];
    }

    public function balanceSheet(int $bookId, string $asOf, ?array $dimensions = null): array
    {
        $this->validateDate($asOf);
        $this->assertBook($bookId);
        $rows = $this->accounts($bookId, $asOf, null, ['asset', 'liability', 'equity', 'bank', 'cash', 'current_asset', 'fixed_asset', 'current_liability', 'long_term_liability'], $dimensions);
        $group = static fn (array $types): array => array_values(array_filter($rows, static fn (array $row): bool => in_array($row['type'], $types, true)));
        $assets = $group(['asset', 'bank', 'cash', 'current_asset', 'fixed_asset']);
        $liabilities = $group(['liability', 'current_liability', 'long_term_liability']);
        $equity = $group(['equity']);
        $sum = static fn (array $set): float => round(array_sum(array_column($set, 'balance')), 2);
        $retainedEarnings = $this->profitAndLoss($bookId, '2000-01-01', $asOf, $dimensions)['net_income'];
        $totalAssets = $sum($assets);
        $totalLiabilities = abs($sum($liabilities));
        $totalEquity = round($sum($equity) + $retainedEarnings, 2);

        return [
            'type' => 'balance_sheet',
            'as_of_date' => $asOf,
            'dimensions' => $dimensions ?? [],
            'assets' => ['accounts' => $assets, 'total' => $totalAssets],
            'liabilities' => ['accounts' => $liabilities, 'total' => $totalLiabilities],
            'equity' => ['accounts' => $equity, 'retained_earnings' => $retainedEarnings, 'total' => $totalEquity],
            'total_liabilities_and_equity' => round($totalLiabilities + $totalEquity, 2),
            'balance_check' => round($totalAssets - $totalLiabilities - $totalEquity, 2),
        ];
    }

    public function cashFlow(int $bookId, string $start, string $end, ?array $dimensions = null): array
    {
        $this->validatePeriod($bookId, $start, $end);
        $cashRows = $this->accounts($bookId, $end, $start, ['bank', 'cash'], $dimensions);
        if ($cashRows === []) {
            // Legacy charts commonly classify the cash account as a generic asset.
            $cashRows = $this->accounts($bookId, $end, $start, ['asset'], $dimensions);
        }
        $beginningRows = $this->accounts($bookId, $this->previousDate($start), null, ['bank', 'cash'], $dimensions);
        $beginningCash = round(array_sum(array_column($beginningRows, 'balance')), 2);
        $endingCash = round(array_sum(array_column($cashRows, 'balance')), 2);
        $netChange = round($endingCash - $beginningCash, 2);

        return [
            'type' => 'cash_flow',
            'period' => ['start_date' => $start, 'end_date' => $end],
            'dimensions' => $dimensions ?? [],
            'operating_activities' => ['accounts' => $cashRows, 'net_cash_from_operations' => $netChange],
            'investing_activities' => ['accounts' => [], 'net_cash_from_investing' => 0.0],
            'financing_activities' => ['accounts' => [], 'net_cash_from_financing' => 0.0],
            'net_change_in_cash' => $netChange,
            'beginning_cash' => $beginningCash,
            'ending_cash' => $endingCash,
        ];
    }

    public function changesInEquity(int $bookId, string $start, string $end, ?array $dimensions = null): array
    {
        $this->validatePeriod($bookId, $start, $end);
        $rows = $this->accounts($bookId, $end, $start, ['equity'], $dimensions);
        $netIncome = $this->profitAndLoss($bookId, $start, $end, $dimensions)['net_income'];

        return [
            'type' => 'changes_in_equity',
            'period' => ['start_date' => $start, 'end_date' => $end],
            'dimensions' => $dimensions ?? [],
            'accounts' => $rows,
            'net_income' => $netIncome,
            'total_change' => round(array_sum(array_column($rows, 'balance')) + $netIncome, 2),
        ];
    }

    public function comparative(int $bookId, string $start, string $end, string $compareStart, string $compareEnd, ?array $dimensions = null): array
    {
        $this->validatePeriod($bookId, $start, $end);
        $this->validatePeriod($bookId, $compareStart, $compareEnd);

        return [
            'type' => 'comparative',
            'current' => $this->profitAndLoss($bookId, $start, $end, $dimensions),
            'comparative' => $this->profitAndLoss($bookId, $compareStart, $compareEnd, $dimensions),
        ];
    }

    public function drillThrough(int $bookId, int $accountId, string $start, string $end, ?array $dimensions = null): array
    {
        $this->validatePeriod($bookId, $start, $end);
        $query = $this->lines($bookId, $end, $start, $dimensions)->where('a.id', $accountId);

        return $query->orderBy('j.entry_date')->select(
            'j.id as journal_id', 'j.entry_number', 'j.entry_date', 'j.description',
            'l.debit', 'l.credit', 'l.description as line_description', 'l.dimensions',
        )->get()->map(static fn ($row): array => (array) $row)->all();
    }

    private function lines(int $bookId, string $end, ?string $start = null, ?array $dimensions = null): Builder
    {
        $this->validateDate($end);
        $this->assertDimensions($dimensions);
        $query = DB::table('accounting_journal_lines as l')
            ->join('accounting_journal_entries as j', 'j.id', '=', 'l.journal_entry_id')
            ->join('accounting_chart_accounts as a', 'a.id', '=', 'l.account_id')
            ->where('j.book_id', $bookId)
            ->where('j.status', 'posted')
            ->whereDate('j.entry_date', '<=', $end)
            ->when($start, fn (Builder $builder): Builder => $builder->whereDate('j.entry_date', '>=', $start));

        foreach ($dimensions ?? [] as $key => $value) {
            $query->whereJsonContains('l.dimensions->'.$key, $value);
        }

        return $query;
    }

    private function accounts(int $bookId, string $end, ?string $start = null, ?array $types = null, ?array $dimensions = null): array
    {
        return $this->lines($bookId, $end, $start, $dimensions)
            ->when($types, fn (Builder $query): Builder => $query->whereIn('a.type', $types))
            ->groupBy('a.id', 'a.code', 'a.name', 'a.type', 'a.normal_balance')
            ->select('a.id', 'a.code', 'a.name', 'a.type', 'a.normal_balance', DB::raw('SUM(l.debit) as debits'), DB::raw('SUM(l.credit) as credits'))
            ->get()->map(static function ($row): array {
                $debit = (float) $row->debits;
                $credit = (float) $row->credits;
                $balance = $row->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;

                return ['account_id' => $row->id, 'code' => $row->code, 'name' => $row->name, 'type' => $row->type, 'debits' => round($debit, 2), 'credits' => round($credit, 2), 'balance' => round($balance, 2)];
            })->values()->all();
    }

    private function validatePeriod(int $bookId, string $start, string $end): void
    {
        $this->assertBook($bookId);
        $this->validateDate($start);
        $this->validateDate($end);
        if ($start > $end) {
            throw new InvalidStatementRequest('The statement start date must not be after its end date.');
        }
    }

    private function validateDate(string $date): void
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidStatementRequest('Statement dates must use the YYYY-MM-DD format.');
        }
    }

    private function assertBook(int $bookId): void
    {
        if ($bookId < 1 || ! DB::table('accounting_books')->where('id', $bookId)->exists()) {
            throw new InvalidStatementRequest('The requested accounting book does not exist.');
        }
    }

    private function assertDimensions(?array $dimensions): void
    {
        foreach ($dimensions ?? [] as $key => $value) {
            if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_.-]{0,63}$/', $key) || ! is_scalar($value)) {
                throw new InvalidStatementRequest('Statement dimensions must be named scalar filters.');
            }
        }
    }

    private function previousDate(string $start): string
    {
        return (new DateTimeImmutable($start))->modify('-1 day')->format('Y-m-d');
    }
}

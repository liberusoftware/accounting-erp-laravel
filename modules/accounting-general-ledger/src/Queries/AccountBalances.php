<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Queries;

use Illuminate\Support\Facades\DB;

final class AccountBalances
{
    public function handle(int $bookId, ?string $through = null): array
    {
        return DB::table('accounting_journal_lines as l')->join('accounting_journal_entries as j', 'j.id', '=', 'l.journal_entry_id')->where('j.book_id', $bookId)->where('j.status', 'posted')->when($through, fn ($q) => $q->where('j.entry_date', '<=', $through))->groupBy('l.account_id')->select('l.account_id', DB::raw('SUM(l.debit) as debits'), DB::raw('SUM(l.credit) as credits'))->get()->map(fn ($row) => ['account_id' => $row->account_id, 'debits' => (string) $row->debits, 'credits' => (string) $row->credits, 'balance' => (string) ((float) $row->debits - (float) $row->credits)])->all();
    }
}

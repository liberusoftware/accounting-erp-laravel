<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Queries;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

final class ControlAccountReconciliationQuery
{
    public function handle(?float $controlBalance = null): array
    {
        $subledger = (float) ReceivableOpenItem::query()->where('status', '!=', 'settled')->get()->sum(fn (ReceivableOpenItem $item) => $item->outstanding());
        $control = $controlBalance ?? null;
        if ($control === null && DB::getSchemaBuilder()->hasTable('accounting_chart_accounts')) {
            $account = DB::table('accounting_chart_accounts')->where('code', 'accounts_receivable')->first();
            if ($account && DB::getSchemaBuilder()->hasTable('accounting_journal_lines')) {
                $control = (float) DB::table('accounting_journal_lines')->join('accounting_journal_entries', 'accounting_journal_entries.id', '=', 'accounting_journal_lines.journal_entry_id')->where('accounting_journal_lines.account_id', $account->id)->where('accounting_journal_entries.status', 'posted')->sum(DB::raw('accounting_journal_lines.debit-accounting_journal_lines.credit'));
            }
        }

        return ['subledger_balance' => $subledger, 'control_account_balance' => $control, 'difference' => $control === null ? null : round($subledger - $control, 2), 'is_reconciled' => $control !== null && abs($subledger - $control) < 0.005];
    }
}

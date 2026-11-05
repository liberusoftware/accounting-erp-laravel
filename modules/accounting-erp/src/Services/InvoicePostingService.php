<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\RevenueSchedule;
use RuntimeException;

/**
 * Posts a customer invoice to the general ledger as a balanced journal entry:
 * Dr Accounts Receivable / Cr Deferred Revenue (when the invoice has a revenue
 * schedule) or Cr Sales Revenue (otherwise), for the pre-tax total.
 *
 * ponytail: per-line tax_amount -> Sales Tax Payable (2200) is deferred to a
 * later slice; this posts total_amount only (balanced pre-tax).
 */
class InvoicePostingService
{
    public function post(Invoice $invoice): JournalEntry
    {
        if ($invoice->journal_entry_id !== null) {
            $existing = $invoice->journalEntry;

            return $existing instanceof JournalEntry ? $existing : throw new RuntimeException('Invoice linked to a missing journal entry.');
        }

        $teamId = (int) $invoice->team_id;
        $receivable = $this->resolveByNumber($teamId, 1100);

        $schedule = RevenueSchedule::where('invoice_id', $invoice->getKey())->first();
        $credit = $schedule instanceof RevenueSchedule
            ? $this->resolveById($teamId, (int) $schedule->deferred_account_id)
            : $this->resolveByNumber($teamId, 4000);

        // ponytail: RevenueSchedule.total_amount is frozen at schedule-creation time;
        // if line items change between scheduling and posting, this posts the invoice's
        // current total while the schedule still recognizes the frozen amount, leaving
        // a residual in Deferred Revenue. Accepted for this slice.
        $amount = $invoice->total_amount;

        return DB::transaction(function () use ($invoice, $receivable, $credit, $amount, $teamId, $schedule): JournalEntry {
            $locked = Invoice::whereKey($invoice->getKey())->lockForUpdate()->first();
            if ($locked instanceof Invoice && $locked->journal_entry_id !== null) {
                $existing = $locked->journalEntry;

                return $existing instanceof JournalEntry ? $existing : throw new RuntimeException('Invoice linked to a missing journal entry.');
            }

            $entry = new JournalEntry();
            // team_id + user_id are NOT fillable and there is no auth() here; set
            // them explicitly so the entry is team-scoped (never default team 1)
            // and satisfies the non-nullable user_id FK (team owner).
            $entry->forceFill([
                'entry_date' => $invoice->invoice_date,
                'entry_type' => 'general',
                'reference_number' => (string) $invoice->getKey(),
                'memo' => 'Invoice '.$invoice->invoice_number,
                'team_id' => $teamId,
                'user_id' => $invoice->team?->user_id,
            ])->save();

            $entry->lines()->create([
                'account_id' => $receivable->getKey(),
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'description' => 'Accounts Receivable',
            ]);
            $entry->lines()->create([
                'account_id' => $credit->getKey(),
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'description' => $schedule instanceof RevenueSchedule ? 'Deferred revenue' : 'Sales revenue',
            ]);

            $entry->post();

            $invoice->forceFill(['journal_entry_id' => $entry->getKey()])->save();

            return $entry;
        });
    }

    private function resolveByNumber(int $teamId, int $number): Account
    {
        $account = Account::where('team_id', $teamId)->where('account_number', $number)->first();
        if (! $account instanceof Account) {
            throw new RuntimeException("Account {$number} not found for team {$teamId}. Provision the chart of accounts first (tenants:provision-chart).");
        }

        return $account;
    }

    private function resolveById(int $teamId, int $id): Account
    {
        $account = Account::where('team_id', $teamId)->where('id', $id)->first();
        if (! $account instanceof Account) {
            throw new RuntimeException("Revenue-schedule account {$id} not found for team {$teamId}.");
        }

        return $account;
    }
}

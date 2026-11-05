<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Invoice;
use Liberu\Accounting\Erp\Models\JournalEntry;
use Liberu\Accounting\Erp\Models\RevenueSchedule;

class RevenueRecognitionService
{
    public function createFromInvoice(Invoice $invoice, int $periods, Account $deferred, Account $revenue): RevenueSchedule
    {
        if ($periods < 1) {
            throw new InvalidArgumentException('periods must be at least 1.');
        }
        if (RevenueSchedule::where('invoice_id', $invoice->getKey())->exists()) {
            throw new InvalidArgumentException('A revenue schedule already exists for this invoice.');
        }
        if ($deferred->is($revenue)) {
            throw new InvalidArgumentException('Deferred and revenue accounts must be different.');
        }
        if ($invoice->journal_entry_id !== null) {
            throw new InvalidArgumentException('Cannot create a revenue schedule for an invoice already posted to the ledger.');
        }

        $total = (float) $invoice->total_amount;
        $per = round($total / $periods, 2);
        $start = $invoice->invoice_date->copy();

        return DB::transaction(function () use ($invoice, $periods, $deferred, $revenue, $total, $per, $start): RevenueSchedule {
            $schedule = RevenueSchedule::create([
                'invoice_id' => $invoice->getKey(),
                'total_amount' => $total,
                'start_date' => $start,
                'periods' => $periods,
                'deferred_account_id' => $deferred->getKey(),
                'revenue_account_id' => $revenue->getKey(),
                'status' => 'active',
                'team_id' => $invoice->team_id,
            ]);

            for ($n = 1; $n <= $periods; $n++) {
                $amount = $n < $periods ? $per : round($total - $per * ($periods - 1), 2);
                $schedule->entries()->create([
                    'period_number' => $n,
                    'recognition_date' => $start->copy()->addMonths($n - 1),
                    'amount' => $amount,
                    'recognized' => false,
                ]);
            }

            return $schedule;
        });
    }

    public function recognizeDue(RevenueSchedule $schedule): int
    {
        if ($schedule->status !== 'active') {
            return 0;
        }
        if ($schedule->invoice?->journal_entry_id === null) {
            return 0; // invoice not yet posted to the ledger -> deferred revenue not funded
        }

        $today = today();
        $count = 0;

        $due = $schedule->entries()
            ->where('recognized', false)
            ->whereDate('recognition_date', '<=', $today)
            ->orderBy('period_number')
            ->get();

        foreach ($due as $entry) {
            DB::transaction(function () use ($schedule, $entry): void {
                $je = new JournalEntry();
                // team_id + user_id are NOT fillable and there is no auth() in the scheduled
                // command; set them explicitly so the entry is team-scoped (never the default
                // team 1) and satisfies the non-nullable user_id FK (team owner).
                $je->forceFill([
                    'entry_date' => $entry->recognition_date,
                    'entry_type' => 'general',
                    'reference_number' => (string) $schedule->getKey(),
                    'memo' => 'Revenue recognition — schedule #'.$schedule->getKey().' period '.$entry->period_number,
                    'team_id' => $schedule->team_id,
                    'user_id' => $schedule->team->user_id,
                ])->save();

                $je->lines()->create([
                    'account_id' => $schedule->deferred_account_id,
                    'debit_amount' => $entry->amount,
                    'credit_amount' => 0,
                    'description' => 'Deferred revenue recognised',
                ]);
                $je->lines()->create([
                    'account_id' => $schedule->revenue_account_id,
                    'debit_amount' => 0,
                    'credit_amount' => $entry->amount,
                    'description' => 'Revenue recognised',
                ]);

                $je->post();

                $entry->update([
                    'recognized' => true,
                    'recognized_at' => now(),
                    'journal_entry_id' => $je->getKey(),
                ]);
            });
            $count++;
        }

        if ($schedule->entries()->where('recognized', false)->count() === 0) {
            $schedule->update(['status' => 'completed']);
        }

        return $count;
    }
}

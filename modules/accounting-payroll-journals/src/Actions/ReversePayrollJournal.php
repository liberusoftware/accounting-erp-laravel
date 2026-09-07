<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollJournals\Enums\JournalStatus;
use Liberu\Accounting\PayrollJournals\Exceptions\InvalidPayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;

final class ReversePayrollJournal
{
    public function handle(PayrollJournal $journal, string $reversalRef): PayrollJournal
    {
        if ($journal->status !== JournalStatus::Posted || blank($reversalRef)) {
            throw new InvalidPayrollJournal('Only posted journals with a reversal reference can be reversed.');
        }

        return DB::transaction(function () use ($journal, $reversalRef): PayrollJournal {
            $journal->update(['status' => JournalStatus::Reversed, 'reversed_at' => now(), 'reversal_ref' => $reversalRef]);

            return $journal->refresh();
        });
    }
}

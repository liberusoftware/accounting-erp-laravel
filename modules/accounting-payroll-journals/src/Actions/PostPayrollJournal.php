<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollJournals\Enums\JournalStatus;
use Liberu\Accounting\PayrollJournals\Exceptions\InvalidPayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;

final class PostPayrollJournal
{
    public function handle(PayrollJournal $journal): PayrollJournal
    {
        if ($journal->status !== JournalStatus::Draft) {
            throw new InvalidPayrollJournal('Only draft payroll journals can be posted.');
        }

        return DB::transaction(function () use ($journal): PayrollJournal {
            $journal->update(['status' => JournalStatus::Posted, 'posted_at' => now()]);

            return $journal->refresh();
        });
    }
}

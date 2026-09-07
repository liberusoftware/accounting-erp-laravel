<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndClose;

final class CloseYearEnd
{
    public function handle(YearEndClose $close, string $closingEntryRef): YearEndClose
    {
        if ($close->status !== YearEndStatus::Open && $close->status !== YearEndStatus::Reopened) {
            throw new InvalidYearEnd('Only open or reopened year ends can be closed.');
        }

        return DB::transaction(function () use ($close, $closingEntryRef): YearEndClose {
            $close->update(['status' => YearEndStatus::Closed, 'closed_at' => now(), 'closing_entry_ref' => $closingEntryRef]);

            return $close->refresh();
        });
    }
}

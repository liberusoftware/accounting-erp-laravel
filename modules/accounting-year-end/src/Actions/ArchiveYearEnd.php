<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;

final class ArchiveYearEnd
{
    public function handle(YearEndPeriod $period): YearEndPeriod
    {
        if ($period->status !== YearEndStatus::Locked) {
            throw new InvalidYearEnd('Only locked year ends can be archived.');
        } $period->update(['status' => YearEndStatus::Archived, 'archived_at' => Carbon::now()]);

        return $period->fresh();
    }
}

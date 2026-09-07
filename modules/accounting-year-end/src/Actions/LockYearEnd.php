<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;

final class LockYearEnd
{
    public function handle(YearEndPeriod $period, int $actor): YearEndPeriod
    {
        if ($period->status === YearEndStatus::Locked || $period->status === YearEndStatus::Archived) {
            throw new InvalidYearEnd('Year end is already closed.');
        } $retained = (float) $period->adjustments()->sum('amount');
        $period->update(['status' => YearEndStatus::Locked, 'retained_earnings' => $retained, 'locked_by' => $actor, 'locked_at' => Carbon::now()]);

        return $period->fresh('adjustments');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

final readonly class PeriodLockChanged
{
    use Dispatchable;

    public function __construct(public AccountingPeriod $period, public bool $locked) {}
}

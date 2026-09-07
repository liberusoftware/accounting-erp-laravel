<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\Core\Models\FiscalCalendar;

final readonly class FiscalCalendarCreated
{
    use Dispatchable;

    public function __construct(public FiscalCalendar $calendar) {}
}

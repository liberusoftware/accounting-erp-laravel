<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\KpiAndGoals\Models\KpiMeasurement;

final class KpiMeasurementRecorded
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly KpiMeasurement $measurement) {}
}

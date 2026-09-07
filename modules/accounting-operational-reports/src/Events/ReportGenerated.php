<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Events;

use Liberu\Accounting\OperationalReports\Models\ReportRun;

final class ReportGenerated
{
    public function __construct(public readonly ReportRun $run) {}
}

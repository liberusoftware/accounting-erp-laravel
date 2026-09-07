<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class ReportApproved
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly ReportPack $report) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Actions;

use Liberu\Accounting\ManagementReporting\Enums\ReportStatus;
use Liberu\Accounting\ManagementReporting\Exceptions\InvalidReport;
use Liberu\Accounting\ManagementReporting\Models\ReportPack;

final class ArchiveReport
{
    public function handle(ReportPack $report): ReportPack
    {
        if (! in_array($report->status, [ReportStatus::Approved, ReportStatus::Delivered], true)) {
            throw new InvalidReport('Only approved or delivered reports can be archived.');
        }$report->update(['status' => ReportStatus::Archived, 'archived_at' => now()]);

        return $report->refresh();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Actions;

use Liberu\Accounting\CustomReportBuilder\Enums\ReportExportStatus;
use Liberu\Accounting\CustomReportBuilder\Exceptions\InvalidCustomReport;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReport;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReportExport;

final class RequestReportExport
{
    public function handle(CustomReport $report, string $format, array $parameters = []): CustomReportExport
    {
        if (! in_array($format, ['csv', 'xlsx', 'pdf'], true)) {
            throw new InvalidCustomReport('Export format is not supported.');
        }

        return $report->exports()->create(['team_id' => $report->team_id, 'format' => $format, 'status' => ReportExportStatus::Requested, 'parameters' => $parameters]);
    }
}

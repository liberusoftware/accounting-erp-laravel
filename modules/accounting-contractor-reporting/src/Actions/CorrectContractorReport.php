<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Actions;

use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;
use Liberu\Accounting\ContractorReporting\Exceptions\InvalidContractorReport;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;

final class CorrectContractorReport
{
    public function handle(ContractorReport $report, array $correction): ContractorReport
    {
        if ($report->status !== ContractorReportStatus::Filed || blank($correction['reason'] ?? null)) {
            throw new InvalidContractorReport('Only filed reports with a correction reason can be corrected.');
        } $report->update(['status' => ContractorReportStatus::Corrected, 'correction' => $correction]);

        return $report->fresh();
    }
}

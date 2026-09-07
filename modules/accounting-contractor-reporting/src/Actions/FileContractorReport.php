<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Actions;

use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;
use Liberu\Accounting\ContractorReporting\Exceptions\InvalidContractorReport;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;

final class FileContractorReport
{
    public function handle(ContractorReport $report, string $adapter): ContractorReport
    {
        if ($report->status !== ContractorReportStatus::Validated || blank($adapter)) {
            throw new InvalidContractorReport('Only validated reports can be filed.');
        } $report->update(['status' => ContractorReportStatus::Filed, 'filing_adapter' => $adapter]);

        return $report->fresh();
    }
}

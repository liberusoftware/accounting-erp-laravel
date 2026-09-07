<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Actions;

use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;
use Liberu\Accounting\ContractorReporting\Exceptions\InvalidContractorReport;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;

final class ValidateContractorReport
{
    public function handle(ContractorReport $report, array $validation): ContractorReport
    {
        if ($report->status !== ContractorReportStatus::Draft || blank($validation['tax_id'] ?? null) || blank($validation['legal_name'] ?? null)) {
            throw new InvalidContractorReport('Only draft reports with validated payee identity can be validated.');
        } $report->update(['status' => ContractorReportStatus::Validated, 'payee_validation' => $validation]);

        return $report->fresh();
    }
}

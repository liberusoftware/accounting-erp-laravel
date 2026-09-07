<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Actions;

use Liberu\Accounting\ContractorReporting\Enums\ContractorReportStatus;
use Liberu\Accounting\ContractorReporting\Exceptions\InvalidContractorReport;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;

final class CreateContractorReport
{
    public function handle(array $attributes): ContractorReport
    {
        foreach (['team_id', 'payee_ref', 'tax_year', 'classification', 'threshold', 'form_type'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidContractorReport("{$field} is required.");
            }
        } if (! preg_match('/^\d{4}$/', (string) $attributes['tax_year']) || ((float) $attributes['threshold'] < 0) || ((float) ($attributes['reportable_amount'] ?? 0) < 0)) {
            throw new InvalidContractorReport('Tax year and amounts are invalid.');
        }

        return ContractorReport::create([...$attributes, 'status' => ContractorReportStatus::Draft, 'reportable_amount' => $attributes['reportable_amount'] ?? 0]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Actions;

use Liberu\Accounting\ContractorCompliance\Enums\ContractorComplianceStatus;
use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class RegisterContractor
{
    public function handle(array $attributes): Contractor
    {
        foreach (['team_id', 'contractor_ref', 'legal_name', 'classification'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidContractorCompliance("{$field} is required.");
            }
        }

        return Contractor::create([...$attributes, 'status' => ContractorComplianceStatus::Active]);
    }
}

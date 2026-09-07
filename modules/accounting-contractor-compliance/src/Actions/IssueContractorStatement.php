<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Actions;

use Liberu\Accounting\ContractorCompliance\Enums\ContractorComplianceStatus;
use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class IssueContractorStatement
{
    public function handle(Contractor $contractor, array $statement): Contractor
    {
        if ($contractor->status !== ContractorComplianceStatus::Active || blank($statement['period'] ?? null)) {
            throw new InvalidContractorCompliance('Only active contractors with a statement period can receive statements.');
        }

        $contractor->update(['statement' => $statement]);

        return $contractor->refresh();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Actions;

use Liberu\Accounting\ContractorCompliance\Enums\ContractorComplianceStatus;
use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class UpdateContractorCompliance
{
    public function handle(Contractor $contractor, array $attributes): Contractor
    {
        if ($contractor->status === ContractorComplianceStatus::Archived) {
            throw new InvalidContractorCompliance('Archived contractors cannot be changed.');
        }

        $contractor->update($attributes);

        return $contractor->refresh();
    }
}

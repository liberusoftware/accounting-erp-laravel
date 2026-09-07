<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Actions;

use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class RecordContractorEvidence
{
    public function handle(Contractor $contractor, array $evidence): Contractor
    {
        if (blank($evidence['type'] ?? null) || blank($evidence['reference'] ?? null)) {
            throw new InvalidContractorCompliance('Evidence type and reference are required.');
        }

        $contractor->update(['evidence' => [...($contractor->evidence ?? []), $evidence]]);

        return $contractor->refresh();
    }
}

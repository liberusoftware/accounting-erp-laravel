<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Actions;

use Liberu\Accounting\ContractorCompliance\Exceptions\InvalidContractorCompliance;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class ExportContractorCompliance
{
    public function handle(Contractor $contractor, string $region): Contractor
    {
        if (blank($region)) {
            throw new InvalidContractorCompliance('A regional export destination is required.');
        }

        $contractor->update(['regional_export' => ['region' => $region, 'exported_at' => now()->toIso8601String()]]);

        return $contractor->refresh();
    }
}

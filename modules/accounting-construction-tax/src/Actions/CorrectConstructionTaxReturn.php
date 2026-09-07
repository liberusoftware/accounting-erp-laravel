<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Actions;

use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;
use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class CorrectConstructionTaxReturn
{
    public function handle(ConstructionTaxRecord $record, array $correction): ConstructionTaxRecord
    {
        if ($record->return_status !== ConstructionTaxStatus::Submitted->value || blank($correction['reason'] ?? null)) {
            throw new InvalidConstructionTax('Only submitted returns with a correction reason can be corrected.');
        }

        $record->update(['verification_status' => ConstructionTaxStatus::Corrected, 'correction' => $correction]);

        return $record->refresh();
    }
}

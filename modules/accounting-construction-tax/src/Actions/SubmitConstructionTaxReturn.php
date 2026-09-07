<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Actions;

use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;
use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class SubmitConstructionTaxReturn
{
    public function handle(ConstructionTaxRecord $record, string $adapter): ConstructionTaxRecord
    {
        if ($record->verification_status !== ConstructionTaxStatus::Verified || blank($adapter)) {
            throw new InvalidConstructionTax('Only verified records can be submitted with a filing adapter.');
        }

        $record->update(['return_status' => ConstructionTaxStatus::Submitted->value, 'filing_adapter' => $adapter]);

        return $record->refresh();
    }
}

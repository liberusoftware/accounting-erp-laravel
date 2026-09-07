<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Actions;

use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;
use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class VerifySubcontractor
{
    public function handle(ConstructionTaxRecord $record, array $verification): ConstructionTaxRecord
    {
        if ($record->verification_status !== ConstructionTaxStatus::Pending || blank($verification['reference'] ?? null)) {
            throw new InvalidConstructionTax('Only pending records with a verification reference can be verified.');
        }

        $record->update(['verification_status' => ConstructionTaxStatus::Verified, 'verification' => $verification]);

        return $record->refresh();
    }
}

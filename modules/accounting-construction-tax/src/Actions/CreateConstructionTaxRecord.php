<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Actions;

use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;
use Liberu\Accounting\ConstructionTax\Exceptions\InvalidConstructionTax;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class CreateConstructionTaxRecord
{
    public function handle(array $attributes): ConstructionTaxRecord
    {
        foreach (['team_id', 'subcontractor_ref', 'tax_period'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidConstructionTax("{$field} is required.");
            }
        }

        $rate = (float) ($attributes['deduction_rate'] ?? 0);
        $gross = (float) ($attributes['gross_amount'] ?? 0);
        if ($rate < 0 || $rate > 100 || $gross < 0) {
            throw new InvalidConstructionTax('Deduction rate and gross amount are invalid.');
        }

        return ConstructionTaxRecord::create([...$attributes, 'verification_status' => ConstructionTaxStatus::Pending, 'deduction_amount' => $attributes['deduction_amount'] ?? ($gross * $rate / 100)]);
    }
}

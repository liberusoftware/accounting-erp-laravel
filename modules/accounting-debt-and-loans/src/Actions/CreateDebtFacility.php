<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Actions;

use Liberu\Accounting\DebtAndLoans\Enums\DebtFacilityStatus;
use Liberu\Accounting\DebtAndLoans\Exceptions\InvalidDebt;
use Liberu\Accounting\DebtAndLoans\Models\DebtFacility;

final class CreateDebtFacility
{
    public function handle(array $attributes): DebtFacility
    {
        foreach (['team_id', 'facility_ref', 'lender_ref', 'currency', 'limit_amount', 'start_date', 'maturity_date'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidDebt("{$field} is required.");
            }
        }
        if ((float) $attributes['limit_amount'] <= 0 || (float) ($attributes['interest_rate'] ?? 0) < 0) {
            throw new InvalidDebt('Facility limit must be positive and interest cannot be negative.');
        }

        return DebtFacility::create([...$attributes, 'drawn_amount' => 0, 'status' => DebtFacilityStatus::Active]);
    }
}

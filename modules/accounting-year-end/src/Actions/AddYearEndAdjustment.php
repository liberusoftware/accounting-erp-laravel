<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Liberu\Accounting\YearEnd\Enums\YearEndAdjustmentStatus;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndAdjustment;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;

final class AddYearEndAdjustment
{
    public function handle(YearEndPeriod $period, array $attributes): YearEndAdjustment
    {
        if ($period->status === YearEndStatus::Locked || $period->status === YearEndStatus::Archived || (float) ($attributes['amount'] ?? 0) === 0) {
            throw new InvalidYearEnd('Locked periods cannot receive zero-value adjustments.');
        } foreach (['adjustment_ref', 'description'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidYearEnd("{$field} is required.");
            }
        } $period->update(['status' => YearEndStatus::Adjusted]);

        return $period->adjustments()->create([...$attributes, 'team_id' => $period->team_id, 'status' => YearEndAdjustmentStatus::Draft]);
    }
}

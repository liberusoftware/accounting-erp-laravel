<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Actions;

use Liberu\Accounting\YearEnd\Enums\YearEndStatus;
use Liberu\Accounting\YearEnd\Exceptions\InvalidYearEnd;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;

final class CreateYearEndPeriod
{
    public function handle(array $attributes): YearEndPeriod
    {
        foreach (['team_id', 'period_ref', 'period_end'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidYearEnd("{$field} is required.");
            }
        }

        return YearEndPeriod::create([...$attributes, 'status' => YearEndStatus::Open]);
    }
}

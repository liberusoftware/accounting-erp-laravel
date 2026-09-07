<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccounting\Actions;

use Liberu\Accounting\CarbonAccounting\Models\CarbonActivity;

final class RecordCarbonActivity
{
    public function handle(array $attributes): CarbonActivity
    {
        foreach (['team_id', 'activity_date', 'scope', 'category', 'quantity', 'unit', 'emission_factor'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        } $attributes['co2e'] = (string) ((float) $attributes['quantity'] * (float) $attributes['emission_factor']);

        return CarbonActivity::create($attributes);
    }
}

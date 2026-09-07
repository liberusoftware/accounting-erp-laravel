<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class CreateCloseCycle
{
    public function handle(array $attributes): CloseCycle
    {
        foreach (['team_id', 'cycle_ref', 'period', 'due_date'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCloseCycle("{$field} is required.");
            }
        }

        return CloseCycle::create([...$attributes, 'status' => CloseCycleStatus::Open]);
    }
}

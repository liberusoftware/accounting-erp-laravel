<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Actions;

use Liberu\Accounting\Collections\Exceptions\InvalidCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class CreateCollectionCase
{
    public function handle(array $attributes): CollectionCase
    {
        foreach (['team_id', 'case_ref', 'customer_ref', 'balance'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCollectionCase("{$field} is required.");
            }
        }

        if ((float) $attributes['balance'] < 0 || (float) ($attributes['interest_rate'] ?? 0) < 0) {
            throw new InvalidCollectionCase('Balance and interest rate cannot be negative.');
        }

        return CollectionCase::create($attributes);
    }
}

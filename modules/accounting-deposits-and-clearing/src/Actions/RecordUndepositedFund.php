<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Actions;

use Liberu\Accounting\DepositsAndClearing\Enums\ClearingFundStatus;
use Liberu\Accounting\DepositsAndClearing\Exceptions\InvalidClearing;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingFund;

final class RecordUndepositedFund
{
    public function handle(array $attributes): ClearingFund
    {
        foreach (['team_id', 'source_type', 'source_id', 'amount', 'currency', 'received_on'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidClearing("{$field} is required.");
            }
        }
        if ((float) $attributes['amount'] <= 0) {
            throw new InvalidClearing('Fund amount must be positive.');
        }

        return ClearingFund::create([...$attributes, 'status' => ClearingFundStatus::Undeposited]);
    }
}

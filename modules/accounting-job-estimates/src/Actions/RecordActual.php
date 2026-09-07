<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\EstimateActual;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class RecordActual
{
    public function handle(JobEstimate $estimate, array $attributes): EstimateActual
    {
        $amount = (float) ($attributes['amount'] ?? -1);
        foreach (['category', 'source_ref', 'occurred_at'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidEstimate("Missing actual field [{$key}].");
            }
        }if ($amount < 0) {
            throw new InvalidEstimate('Actual amount cannot be negative.');
        }$actual = EstimateActual::create(['estimate_id' => $estimate->getKey(), 'version_id' => $attributes['version_id'] ?? null, 'line_ref' => $attributes['line_ref'] ?? null, 'category' => $attributes['category'], 'amount' => $amount, 'source_ref' => $attributes['source_ref'], 'occurred_at' => $attributes['occurred_at'], 'metadata' => $attributes['metadata'] ?? null]);
        if ($actual->line_ref) {
            $estimate->lines()->where('line_ref', $actual->line_ref)->increment('actual_amount', $amount);
        }

        return $actual;
    }
}

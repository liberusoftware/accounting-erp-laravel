<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Enums\LineType;
use Liberu\Accounting\JobEstimates\Exceptions\InvalidEstimate;
use Liberu\Accounting\JobEstimates\Models\EstimateLine;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class AddEstimateLine
{
    public function handle(JobEstimate $estimate, array $attributes): EstimateLine
    {
        $type = LineType::tryFrom((string) ($attributes['line_type'] ?? ''));
        $quantity = (float) ($attributes['quantity'] ?? 0);
        $rate = (float) ($attributes['rate'] ?? 0);
        foreach (['line_ref', 'category', 'description'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidEstimate("Missing line field [{$key}].");
            }
        }if (! $type || $quantity < 0 || $rate < 0) {
            throw new InvalidEstimate('Line type, quantity and rate must be valid non-negative values.');
        }if (! in_array($estimate->status, [EstimateStatus::Draft, EstimateStatus::Rejected], true)) {
            throw new InvalidEstimate('Lines can only be changed on a draft estimate.');
        }

        return DB::transaction(function () use ($estimate, $attributes, $type, $quantity, $rate): EstimateLine {
            $line = EstimateLine::create(['estimate_id' => $estimate->getKey(), 'version_id' => null, 'line_ref' => $attributes['line_ref'], 'line_type' => $type, 'category' => $attributes['category'], 'description' => $attributes['description'], 'quantity' => $quantity, 'rate' => $rate, 'amount' => round($quantity * $rate, 2), 'metadata' => $attributes['metadata'] ?? null]);
            $this->totals($estimate);

            return $line;
        });
    }

    private function totals(JobEstimate $estimate): void
    {
        $cost = (float) $estimate->lines()->where('line_type', LineType::Cost)->sum('amount');
        $revenue = (float) $estimate->lines()->where('line_type', LineType::Revenue)->sum('amount');
        $estimate->update(['total_cost' => $cost, 'total_revenue' => $revenue]);
    }
}

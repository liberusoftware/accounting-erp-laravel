<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Dimensions\Exceptions\InvalidDimension;
use Liberu\Accounting\Dimensions\Models\DimensionAllocation;

final class AllocateDimensions
{
    public function handle(string $key, string|int|float $amount, array $allocations, ?string $currency = null, ?string $actor = null): array
    {
        if (blank($key) || $allocations === [] || (float) $amount <= 0) {
            throw new InvalidDimension('An allocation requires a key, positive amount, and at least one row.');
        }$total = array_sum(array_map(fn ($row) => (float) ($row['percentage'] ?? 0), $allocations));
        if (abs($total - 100) > 0.0001) {
            throw new InvalidDimension('Dimension allocation percentages must total 100.');
        }app(ValidateDimensions::class)->handle(collect($allocations)->pluck('dimensions')->map(fn ($values) => (array) $values)->reduce(fn (array $carry, array $values): array => array_replace_recursive($carry, $values), []));

        return DB::transaction(function () use ($key, $amount, $allocations, $currency, $actor) {
            if (DimensionAllocation::where('allocation_key', $key)->exists()) {
                throw new InvalidDimension('This allocation key has already been processed.');
            }

            return collect($allocations)->map(fn ($row) => DimensionAllocation::create(['allocation_key' => $key, 'amount' => round((float) $amount * (float) $row['percentage'] / 100, 2), 'currency' => $currency, 'percentage' => $row['percentage'], 'dimensions' => $row['dimensions'] ?? [], 'created_by' => $actor]))->all();
        });
    }
}

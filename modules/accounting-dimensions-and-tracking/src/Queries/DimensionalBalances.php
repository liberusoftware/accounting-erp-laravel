<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Queries;

use Liberu\Accounting\Dimensions\Models\DimensionAllocation;

final class DimensionalBalances
{
    public function handle(?string $key = null): array
    {
        return DimensionAllocation::query()->when($key, fn ($q) => $q->where('allocation_key', $key))->get()->groupBy(fn ($row) => json_encode($row->dimensions, JSON_THROW_ON_ERROR))->map(fn ($rows) => ['dimensions' => $rows->first()->dimensions, 'amount' => (string) $rows->sum('amount')])->values()->all();
    }
}

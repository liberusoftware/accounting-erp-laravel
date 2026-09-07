<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

final class EstimateQuery
{
    public function paginate(?int $legalEntityId = null, ?EstimateStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Estimate::query()->when($legalEntityId !== null, fn ($q) => $q->where('legal_entity_id', $legalEntityId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['items', 'versions', 'history'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function total(Estimate $e): float
    {
        return (float) $e->items()->sum('amount');
    }
}

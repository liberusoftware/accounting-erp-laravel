<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;

final class SalesTaxRecordQuery
{
    public function paginate(?string $context = null, ?string $type = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return SalesTaxRecord::query()->when($context, fn ($q) => $q->where('context_id', $context))->when($type, fn ($q) => $q->where('type', $type))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

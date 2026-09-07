<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;

final class PortalResourceQuery
{
    public function paginate(?string $supplierId = null, ?string $type = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return PortalResource::query()->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))->when($type, fn ($q) => $q->where('type', $type))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

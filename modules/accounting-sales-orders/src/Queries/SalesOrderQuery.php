<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

final class SalesOrderQuery
{
    public function paginate(?string $customer = null, ?string $status = null, int $perPage = 25, ?int $teamId = null): LengthAwarePaginator
    {
        return SalesOrder::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($customer, fn ($q) => $q->where('customer_id', $customer))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

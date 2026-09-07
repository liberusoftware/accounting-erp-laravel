<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;

final class CustomerPaymentQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CustomerPayment::query()->where('team_id', $teamId)->with('allocations')->latest()->get();
    }

    public function forCustomer(int $teamId, string $customerId): Collection
    {
        return CustomerPayment::query()->where('team_id', $teamId)->where('customer_id', $customerId)->with('allocations')->latest()->get();
    }
}

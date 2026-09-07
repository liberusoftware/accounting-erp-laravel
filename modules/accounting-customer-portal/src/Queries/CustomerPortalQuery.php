<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\CustomerPortal\Models\CustomerPortalRecord;

final class CustomerPortalQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CustomerPortalRecord::query()->where('team_id', $teamId)->latest()->get();
    }

    public function forCustomer(int $teamId, string $customerId): Collection
    {
        return CustomerPortalRecord::query()->where('team_id', $teamId)->forCustomer($customerId)->latest()->get();
    }
}

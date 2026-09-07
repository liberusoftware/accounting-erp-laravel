<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\Leases\Models\Lease;

final class LeaseQuery
{
    public function leases(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = Lease::query()->withCount(['payments', 'modifications'])->latest();
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }if ($status !== null) {
            $q->where('status', $status);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function disclosure(Lease $lease): array
    {
        return ['lease_ref' => $lease->lease_ref, 'currency' => $lease->currency, 'liability' => (float) $lease->lease_liability, 'right_of_use_asset' => (float) $lease->right_of_use_asset, 'accumulated_depreciation' => (float) $lease->accumulated_depreciation, 'scheduled_payments' => $lease->payments()->where('status', 'scheduled')->count()];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Models\MileageRate;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class MileageQuery
{
    public function trips(?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        $query = MileageTrip::query()->latest('trip_date');
        if ($teamId !== null) {
            $query->where('team_id', $teamId);
        }

        return $query->paginate(min(max($perPage, 1), 100));
    }

    public function rates(?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        $query = MileageRate::query()->latest('effective_from');
        if ($teamId !== null) {
            $query->where('team_id', $teamId);
        }

        return $query->paginate(min(max($perPage, 1), 100));
    }

    public function regionalReport(?int $teamId = null, ?string $region = null): array
    {
        $query = MileageTrip::query()->whereIn('status', ['approved', 'reimbursed']);
        if ($teamId !== null) {
            $query->where('team_id', $teamId);
        }if ($region !== null) {
            $query->where('region', $region);
        }

        return ['trip_count' => (clone $query)->count(), 'total_distance' => (float) (clone $query)->sum('distance'), 'total_reimbursement' => (float) (clone $query)->sum('reimbursement_amount'), 'by_region' => $query->select('region', DB::raw('COUNT(*) as trip_count'), DB::raw('SUM(distance) as total_distance'), DB::raw('SUM(reimbursement_amount) as total_reimbursement'))->groupBy('region')->get()->toArray()];
    }
}

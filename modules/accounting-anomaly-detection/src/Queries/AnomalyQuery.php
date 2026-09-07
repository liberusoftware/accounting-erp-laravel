<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;

final class AnomalyQuery
{
    public function paginate(int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Anomaly::query()->where('team_id', $teamId)->when($status, fn ($q) => $q->where('status', $status))->latest('detected_at')->paginate(min(max($perPage, 1), 100));
    }
}

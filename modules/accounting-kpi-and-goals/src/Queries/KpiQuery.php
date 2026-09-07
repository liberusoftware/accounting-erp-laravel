<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;
use Liberu\Accounting\KpiAndGoals\Models\KpiMeasurement;

final class KpiQuery
{
    public function goals(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = KpiGoal::query()->with(['metric'])->withCount(['measurements', 'alerts', 'commentary'])->latest('period_end');
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }if ($status !== null) {
            $q->where('status', $status);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function progress(KpiGoal $goal): array
    {
        $latest = KpiMeasurement::query()->where('goal_id', $goal->id)->latest('measured_on')->first();

        return ['goal_ref' => $goal->goal_ref, 'target' => (float) $goal->target, 'latest_value' => $latest ? (float) $latest->value : null, 'progress' => $latest ? (float) $latest->progress : 0, 'status' => $goal->status->value, 'open_alerts' => $goal->alerts()->where('status', 'open')->count()];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal; /** @mixin KpiGoal */
final class KpiGoalResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-kpi-goal', 'attributes' => ['goal_ref' => $this->resource->goal_ref, 'name' => $this->resource->name, 'owner_ref' => $this->resource->owner_ref, 'period_start' => $this->resource->period_start?->toDateString(), 'period_end' => $this->resource->period_end?->toDateString(), 'baseline' => (float) $this->resource->baseline, 'target' => (float) $this->resource->target, 'status' => $this->resource->status?->value, 'metric_id' => $this->resource->metric_id]];
    }
}

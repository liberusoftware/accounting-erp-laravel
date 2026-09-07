<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\MultiEntity\Models\EntityBook;

final class EntityQuery
{
    public function paginate(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return EntityBook::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function report(EntityBook $entity): array
    {
        return ['entity_ref' => $entity->entity_ref, 'code' => $entity->code, 'name' => $entity->name, 'base_currency' => $entity->base_currency, 'access_count' => $entity->access()->count(), 'policy_count' => $entity->policies()->count(), 'open_periods' => $entity->periods()->where('status', 'open')->count(), 'mapping_count' => $entity->mappings()->where('is_active', true)->count()];
    }
}

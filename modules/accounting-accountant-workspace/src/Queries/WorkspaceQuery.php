<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspace\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\AccountantWorkspace\Models\WorkspaceItem;

final class WorkspaceQuery
{
    public function paginate(int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return WorkspaceItem::query()->where('team_id', $teamId)->when($status, fn ($q) => $q->where('status', $status))->orderByRaw('next_deadline is null')->orderBy('next_deadline')->paginate(min(max($perPage, 1), 100));
    }
}

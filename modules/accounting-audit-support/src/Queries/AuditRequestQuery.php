<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;

final class AuditRequestQuery
{
    public function paginate(int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return AuditRequest::query()->where('team_id', $teamId)->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

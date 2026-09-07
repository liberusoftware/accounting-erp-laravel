<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\OperationalReports\Models\ReportRun;

final class ReportQuery
{
    public function paginate(?int $teamId = null, ?string $category = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return ReportRun::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($category, fn ($q) => $q->where('category', $category))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function exceptions(?int $teamId = null): LengthAwarePaginator
    {
        return ReportRun::query()->whereHas('exceptions', fn ($q) => $q->where('status', 'open'))->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->latest()->paginate(25);
    }
}

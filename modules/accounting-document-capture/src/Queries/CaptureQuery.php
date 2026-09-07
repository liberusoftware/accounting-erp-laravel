<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class CaptureQuery
{
    public function paginate(?int $teamId = null, ?CaptureStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return CapturedDocument::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with('events')->latest()->paginate(min(max($perPage, 1), 100));
    }
}

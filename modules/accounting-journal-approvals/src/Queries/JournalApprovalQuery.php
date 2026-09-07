<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\JournalApprovals\Enums\ApprovalStatus;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

final class JournalApprovalQuery
{
    public function paginate(?int $teamId = null, ?ApprovalStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return JournalApproval::query()
            ->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->with(['decisions', 'evidence'])
            ->latest('submitted_at')
            ->paginate(min(max($perPage, 1), 100));
    }
}

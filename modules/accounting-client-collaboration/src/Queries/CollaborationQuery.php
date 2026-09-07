<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\ClientCollaboration\Models\CollaborationThread;

final class CollaborationQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CollaborationThread::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

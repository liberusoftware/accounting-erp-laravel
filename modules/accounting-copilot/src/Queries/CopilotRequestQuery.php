<?php

declare(strict_types=1);

namespace Liberu\Accounting\Copilot\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\Copilot\Models\CopilotRequest;

final class CopilotRequestQuery
{
    public function forTeam(int $teamId, ?string $kind = null): Collection
    {
        return CopilotRequest::query()->where('team_id', $teamId)->when($kind, fn ($query) => $query->where('kind', $kind))->latest()->get();
    }
}

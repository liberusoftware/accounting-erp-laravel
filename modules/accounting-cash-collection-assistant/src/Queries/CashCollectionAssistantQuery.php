<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;

final class CashCollectionAssistantQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CashCollectionAssistant::query()->where('team_id', $teamId)->orderByDesc('risk_score')->latest('id')->get();
    }
}

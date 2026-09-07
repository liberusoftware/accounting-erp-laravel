<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\CorporateCards\Models\CardAccount;

final class CorporateCardQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CardAccount::query()->where('team_id', $teamId)->with('transactions')->latest()->get();
    }
}

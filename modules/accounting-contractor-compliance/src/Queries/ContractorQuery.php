<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\ContractorCompliance\Models\Contractor;

final class ContractorQuery
{
    public function forTeam(int $teamId): Collection
    {
        return Contractor::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

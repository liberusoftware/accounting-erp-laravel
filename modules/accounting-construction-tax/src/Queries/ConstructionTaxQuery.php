<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\ConstructionTax\Models\ConstructionTaxRecord;

final class ConstructionTaxQuery
{
    public function forTeam(int $teamId): Collection
    {
        return ConstructionTaxRecord::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

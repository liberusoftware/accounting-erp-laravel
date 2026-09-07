<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\CodingSuggestions\Models\CodingSuggestion;

final class CodingSuggestionQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CodingSuggestion::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class MatchingQuery
{
    public function suggestions(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = MatchingSuggestion::query()->with(['evidence', 'feedback'])->latest();
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }if ($status !== null) {
            $q->where('status', $status);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }

    public function automationEligible(?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        $q = MatchingSuggestion::query()->where('status', 'suggested')->whereColumn('confidence', '>=', 'automation_threshold')->whereNotNull('automation_threshold')->latest();
        if ($teamId !== null) {
            $q->where('team_id', $teamId);
        }

        return $q->paginate(min(max($perPage, 1), 100));
    }
}

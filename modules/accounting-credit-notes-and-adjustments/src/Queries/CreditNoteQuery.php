<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNote;

final class CreditNoteQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CreditNote::query()->where('team_id', $teamId)->with('allocations')->latest()->get();
    }
}

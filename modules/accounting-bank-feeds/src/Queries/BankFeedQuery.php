<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

final class BankFeedQuery
{
    public function connections(?int $teamId = null): Builder
    {
        return BankFeedConnection::query()->with('institution')->when($teamId !== null, fn (Builder $query): Builder => $query->where('team_id', $teamId))->latest();
    }
}

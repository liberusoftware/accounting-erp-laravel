<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEvents\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\AssetEvents\Models\AssetEvent;

final class AssetEventQuery
{
    public function paginate(int $teamId, ?int $assetId = null, int $perPage = 25): LengthAwarePaginator
    {
        return AssetEvent::query()->where('team_id', $teamId)->when($assetId, fn ($q) => $q->where('asset_id', $assetId))->latest('event_date')->latest('id')->paginate(min(max($perPage, 1), 100));
    }
}

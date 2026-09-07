<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class AssetQuery
{
    public function paginate(?int $teamId = null, ?AssetStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Asset::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['category', 'components', 'books', 'documents'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function registerSummary(Asset $asset): array
    {
        return ['asset_ref' => $asset->asset_ref, 'cost' => (float) $asset->cost, 'salvage_value' => (float) $asset->salvage_value, 'net_book_value' => (float) $asset->net_book_value, 'status' => $asset->status->value, 'books' => $asset->books->count(), 'documents' => $asset->documents->count()];
    }
}

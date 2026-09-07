<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetArchived;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class ArchiveAsset
{
    public function handle(Asset $asset): Asset
    {
        if ($asset->status !== AssetStatus::Disposed) {
            throw new InvalidAsset('Only disposed assets can be archived.');
        }

        $asset->update(['status' => AssetStatus::Archived]);
        $asset = $asset->refresh();
        event(new AssetArchived($asset));

        return $asset;
    }
}

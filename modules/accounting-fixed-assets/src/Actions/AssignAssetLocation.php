<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Liberu\Accounting\FixedAssets\Events\AssetLocationAssigned;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetLocation;

final class AssignAssetLocation
{
    public function handle(Asset $asset, AssetLocation $location): Asset
    {
        $this->assertTenant($asset->team_id, $location->team_id);
        $asset->update(['location_id' => $location->getKey(), 'location_ref' => $location->location_ref]);
        $asset = $asset->refresh();
        event(new AssetLocationAssigned($asset, $location));

        return $asset;
    }

    private function assertTenant(?int $assetTeam, ?int $locationTeam): void
    {
        if ($assetTeam !== $locationTeam) {
            throw new InvalidAsset('The location belongs to a different team.');
        }
    }
}

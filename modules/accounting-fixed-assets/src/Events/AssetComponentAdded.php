<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetComponent;

final readonly class AssetComponentAdded implements ShouldDispatchAfterCommit
{
    public function __construct(public Asset $asset, public AssetComponent $component) {}
}

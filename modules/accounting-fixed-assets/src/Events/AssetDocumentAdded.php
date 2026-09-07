<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetDocument;

final readonly class AssetDocumentAdded implements ShouldDispatchAfterCommit
{
    public function __construct(public Asset $asset, public AssetDocument $document) {}
}

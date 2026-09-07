<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\FixedAssetsFilament\Resources\AssetResource;

final class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\AssetEventsFilament\Resources\AssetEventResource;

final class ListAssetEvents extends ListRecords
{
    protected static string $resource = AssetEventResource::class;
}

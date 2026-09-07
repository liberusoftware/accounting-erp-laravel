<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\WorkpapersFilament\Resources\WorkpaperResource;

final class ListWorkpapers extends ListRecords
{
    protected static string $resource = WorkpaperResource::class;
}

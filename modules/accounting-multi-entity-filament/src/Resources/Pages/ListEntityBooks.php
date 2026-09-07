<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\MultiEntityFilament\Resources\EntityBookResource;

final class ListEntityBooks extends ListRecords
{
    protected static string $resource = EntityBookResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsFilament\Resources\DimensionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\DimensionsFilament\Resources\DimensionResource;

final class ListDimensions extends ListRecords
{
    protected static string $resource = DimensionResource::class;
}

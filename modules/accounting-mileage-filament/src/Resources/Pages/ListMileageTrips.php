<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\MileageFilament\Resources\MileageTripResource;

final class ListMileageTrips extends ListRecords
{
    protected static string $resource = MileageTripResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\CarbonAccountingFilament\Resources\CarbonActivityResource;

final class ListCarbonActivities extends ListRecords
{
    protected static string $resource = CarbonActivityResource::class;
}

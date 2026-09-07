<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\WorkforceCostingFilament\Resources\WorkforceCostResource;

final class ListWorkforceCosts extends ListRecords
{
    protected static string $resource = WorkforceCostResource::class;
}

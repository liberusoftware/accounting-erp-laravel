<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ProjectCostingFilament\Resources\ProjectCostResource;

final class ListProjectCosts extends ListRecords
{
    protected static string $resource = ProjectCostResource::class;
}

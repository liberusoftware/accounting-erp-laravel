<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\EstimateResource;

class ListEstimates extends ListRecords
{
    #[\Override]
    protected static string $resource = EstimateResource::class;
}

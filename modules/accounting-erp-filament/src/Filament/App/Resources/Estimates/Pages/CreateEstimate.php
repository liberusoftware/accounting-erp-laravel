<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\EstimateResource;

class CreateEstimate extends CreateRecord
{
    #[\Override]
    protected static string $resource = EstimateResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Estimates\EstimateResource;

class EditEstimate extends EditRecord
{
    #[\Override]
    protected static string $resource = EstimateResource::class;
}

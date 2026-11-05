<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Plans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Plans\PlanResource;

class CreatePlan extends CreateRecord
{
    #[\Override]
    protected static string $resource = PlanResource::class;
}

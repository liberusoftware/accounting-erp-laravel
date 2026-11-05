<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\ForecastScenarios\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ForecastScenarios\ForecastScenarioResource;

class CreateForecastScenario extends CreateRecord
{
    #[\Override]
    protected static string $resource = ForecastScenarioResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ForecastsFilament\Resources\ForecastResource;

final class ListForecasts extends ListRecords
{
    protected static string $resource = ForecastResource::class;
}

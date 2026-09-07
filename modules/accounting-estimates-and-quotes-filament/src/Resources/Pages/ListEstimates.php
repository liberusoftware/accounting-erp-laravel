<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\EstimatesAndQuotesFilament\Resources\EstimateResource;

final class ListEstimates extends ListRecords
{
    protected static string $resource = EstimateResource::class;
}

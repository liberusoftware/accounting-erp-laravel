<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\MultiCurrencyFilament\Resources\RevaluationResource;

final class ListRevaluations extends ListRecords
{
    protected static string $resource = RevaluationResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\TaxReturnsFilament\Resources\TaxReturnResource;

final class ListTaxReturns extends ListRecords
{
    protected static string $resource = TaxReturnResource::class;
}

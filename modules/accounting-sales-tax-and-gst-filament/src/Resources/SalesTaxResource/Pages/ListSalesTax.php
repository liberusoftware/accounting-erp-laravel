<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstFilament\Resources\SalesTaxResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SalesTaxAndGstFilament\Resources\SalesTaxResource;

final class ListSalesTax extends ListRecords
{
    protected static string $resource = SalesTaxResource::class;
}

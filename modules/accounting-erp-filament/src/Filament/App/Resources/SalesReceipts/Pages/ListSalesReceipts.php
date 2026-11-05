<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesReceipts\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesReceipts\SalesReceiptResource;

class ListSalesReceipts extends ListRecords
{
    #[\Override]
    protected static string $resource = SalesReceiptResource::class;
}

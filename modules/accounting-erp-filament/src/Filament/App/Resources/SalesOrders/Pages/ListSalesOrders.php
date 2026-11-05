<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\SalesOrderResource;

class ListSalesOrders extends ListRecords
{
    #[\Override]
    protected static string $resource = SalesOrderResource::class;
}

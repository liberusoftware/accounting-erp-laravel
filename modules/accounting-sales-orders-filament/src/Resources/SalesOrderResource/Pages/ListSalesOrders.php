<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersFilament\Resources\SalesOrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SalesOrdersFilament\Resources\SalesOrderResource;

final class ListSalesOrders extends ListRecords
{
    protected static string $resource = SalesOrderResource::class;
}

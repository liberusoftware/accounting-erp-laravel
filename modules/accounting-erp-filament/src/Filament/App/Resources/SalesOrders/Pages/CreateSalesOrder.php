<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\SalesOrderResource;

class CreateSalesOrder extends CreateRecord
{
    #[\Override]
    protected static string $resource = SalesOrderResource::class;
}

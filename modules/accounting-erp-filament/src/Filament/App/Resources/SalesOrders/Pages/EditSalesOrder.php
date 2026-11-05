<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesOrders\SalesOrderResource;

class EditSalesOrder extends EditRecord
{
    #[\Override]
    protected static string $resource = SalesOrderResource::class;
}

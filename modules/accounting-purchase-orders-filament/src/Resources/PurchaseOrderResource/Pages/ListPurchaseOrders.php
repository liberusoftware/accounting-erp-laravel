<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersFilament\Resources\PurchaseOrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PurchaseOrdersFilament\Resources\PurchaseOrderResource;

final class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;
}

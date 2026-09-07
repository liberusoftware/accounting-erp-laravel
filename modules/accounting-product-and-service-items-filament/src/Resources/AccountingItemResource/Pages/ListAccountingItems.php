<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsFilament\Resources\AccountingItemResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ProductAndServiceItemsFilament\Resources\AccountingItemResource;

final class ListAccountingItems extends ListRecords
{
    protected static string $resource = AccountingItemResource::class;
}

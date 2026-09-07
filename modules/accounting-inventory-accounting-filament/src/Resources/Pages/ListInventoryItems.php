<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\InventoryAccountingFilament\Resources\InventoryItemResource;

final class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;
}

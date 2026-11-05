<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\InventoryItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\InventoryItems\InventoryItemResource;

class CreateInventoryItem extends CreateRecord
{
    #[\Override]
    protected static string $resource = InventoryItemResource::class;
}

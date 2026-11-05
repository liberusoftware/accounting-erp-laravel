<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\InventoryItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\InventoryItems\InventoryItemResource;

class EditInventoryItem extends EditRecord
{
    #[\Override]
    protected static string $resource = InventoryItemResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

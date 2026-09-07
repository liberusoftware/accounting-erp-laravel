<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource;

final class ListItemServices extends ListRecords
{
    protected static string $resource = ItemServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

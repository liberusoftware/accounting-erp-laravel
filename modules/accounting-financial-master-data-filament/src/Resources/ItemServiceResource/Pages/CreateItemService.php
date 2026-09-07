<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource;

final class CreateItemService extends CreateRecord
{
    protected static string $resource = ItemServiceResource::class;
}

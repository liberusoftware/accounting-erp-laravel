<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\ItemServiceResource;

final class EditItemService extends EditRecord
{
    protected static string $resource = ItemServiceResource::class;
}

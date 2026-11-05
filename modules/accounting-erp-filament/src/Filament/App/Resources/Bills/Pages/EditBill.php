<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\BillResource;

class EditBill extends EditRecord
{
    #[\Override]
    protected static string $resource = BillResource::class;
}

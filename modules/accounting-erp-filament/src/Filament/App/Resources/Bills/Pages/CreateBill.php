<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\BillResource;

class CreateBill extends CreateRecord
{
    #[\Override]
    protected static string $resource = BillResource::class;
}

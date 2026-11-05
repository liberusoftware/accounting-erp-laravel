<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\Bills\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\Bills\PortalBillResource;

class ListPortalBills extends ListRecords
{
    #[\Override]
    protected static string $resource = PortalBillResource::class;
}

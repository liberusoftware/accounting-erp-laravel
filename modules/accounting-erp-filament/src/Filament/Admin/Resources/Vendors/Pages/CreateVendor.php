<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Vendors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Vendors\VendorResource;

class CreateVendor extends CreateRecord
{
    #[\Override]
    protected static string $resource = VendorResource::class;
}

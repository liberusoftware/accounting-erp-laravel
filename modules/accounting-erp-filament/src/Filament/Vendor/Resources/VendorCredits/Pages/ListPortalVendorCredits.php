<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\VendorCredits\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\VendorCredits\PortalVendorCreditResource;

class ListPortalVendorCredits extends ListRecords
{
    #[\Override]
    protected static string $resource = PortalVendorCreditResource::class;
}

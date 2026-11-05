<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\VendorCreditResource;

class ListVendorCredits extends ListRecords
{
    #[\Override]
    protected static string $resource = VendorCreditResource::class;
}

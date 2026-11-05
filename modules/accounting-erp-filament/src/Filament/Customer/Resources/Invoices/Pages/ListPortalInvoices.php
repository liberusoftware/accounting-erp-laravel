<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Customer\Resources\Invoices\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Customer\Resources\Invoices\PortalInvoiceResource;

class ListPortalInvoices extends ListRecords
{
    #[\Override]
    protected static string $resource = PortalInvoiceResource::class;
}

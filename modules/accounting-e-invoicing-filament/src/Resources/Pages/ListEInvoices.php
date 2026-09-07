<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\EInvoicingFilament\Resources\EInvoiceResource;

final class ListEInvoices extends ListRecords
{
    protected static string $resource = EInvoiceResource::class;
}

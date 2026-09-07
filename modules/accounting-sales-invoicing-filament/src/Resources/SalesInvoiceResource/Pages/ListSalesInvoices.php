<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingFilament\Resources\SalesInvoiceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SalesInvoicingFilament\Resources\SalesInvoiceResource;

final class ListSalesInvoices extends ListRecords
{
    protected static string $resource = SalesInvoiceResource::class;
}

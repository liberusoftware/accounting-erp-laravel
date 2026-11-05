<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Invoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Invoices\InvoiceResource;

class ListInvoices extends ListRecords
{
    #[\Override]
    protected static string $resource = InvoiceResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

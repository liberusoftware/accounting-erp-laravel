<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\SupplierBillsFilament\Resources\SupplierBillResource;

final class ListSupplierBills extends ListRecords
{
    protected static string $resource = SupplierBillResource::class;
}

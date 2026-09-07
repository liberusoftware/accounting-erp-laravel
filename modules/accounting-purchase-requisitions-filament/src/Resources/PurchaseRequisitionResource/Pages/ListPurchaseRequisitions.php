<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsFilament\Resources\PurchaseRequisitionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\PurchaseRequisitionsFilament\Resources\PurchaseRequisitionResource;

final class ListPurchaseRequisitions extends ListRecords
{
    protected static string $resource = PurchaseRequisitionResource::class;
}

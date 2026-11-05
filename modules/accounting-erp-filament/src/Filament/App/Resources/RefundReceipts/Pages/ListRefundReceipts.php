<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\RefundReceipts\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\RefundReceipts\RefundReceiptResource;

class ListRefundReceipts extends ListRecords
{
    #[\Override]
    protected static string $resource = RefundReceiptResource::class;
}

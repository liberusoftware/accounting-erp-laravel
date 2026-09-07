<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\GoodsAndServiceReceiptsFilament\Resources\ReceiptResource;

final class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;
}

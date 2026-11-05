<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesReceipts\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\Erp\Models\SalesReceipt;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\SalesReceipts\SalesReceiptResource;

/**
 * @property-read SalesReceipt $record
 */
class EditSalesReceipt extends EditRecord
{
    #[\Override]
    protected static string $resource = SalesReceiptResource::class;

    protected function afterSave(): void
    {
        $this->record->calculateTotals();
    }
}

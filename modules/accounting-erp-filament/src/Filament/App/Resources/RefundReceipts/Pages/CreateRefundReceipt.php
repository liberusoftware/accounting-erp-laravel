<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\RefundReceipts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\Erp\Models\RefundReceipt;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\RefundReceipts\RefundReceiptResource;

/**
 * @property-read RefundReceipt $record
 */
class CreateRefundReceipt extends CreateRecord
{
    #[\Override]
    protected static string $resource = RefundReceiptResource::class;

    protected function afterCreate(): void
    {
        $this->record->calculateTotals();
    }
}

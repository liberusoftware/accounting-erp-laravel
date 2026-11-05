<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\Erp\Models\VendorCredit;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\VendorCreditResource;

/**
 * @property-read VendorCredit $record
 */
class EditVendorCredit extends EditRecord
{
    #[\Override]
    protected static string $resource = VendorCreditResource::class;

    protected function afterSave(): void
    {
        $this->record->calculateTotals();
    }
}

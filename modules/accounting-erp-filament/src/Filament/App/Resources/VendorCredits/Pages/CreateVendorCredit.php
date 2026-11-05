<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\Erp\Models\VendorCredit;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\VendorCredits\VendorCreditResource;

/**
 * @property-read VendorCredit $record
 */
class CreateVendorCredit extends CreateRecord
{
    #[\Override]
    protected static string $resource = VendorCreditResource::class;

    protected function afterCreate(): void
    {
        $this->record->calculateTotals();
    }
}

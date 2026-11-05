<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\TaxRates\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\TaxRates\TaxRateResource;

class EditTaxRate extends EditRecord
{
    #[\Override]
    protected static string $resource = TaxRateResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

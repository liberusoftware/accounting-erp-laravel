<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\HmrcVatReturns\HmrcVatReturnResource;

class EditHmrcVatReturn extends EditRecord
{
    #[\Override]
    protected static string $resource = HmrcVatReturnResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

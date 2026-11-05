<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Vendors\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Vendors\VendorResource;

class EditVendor extends EditRecord
{
    #[\Override]
    protected static string $resource = VendorResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

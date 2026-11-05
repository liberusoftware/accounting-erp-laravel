<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\AssetAcquisitionResource;

class EditAssetAcquisition extends EditRecord
{
    #[\Override]
    protected static string $resource = AssetAcquisitionResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

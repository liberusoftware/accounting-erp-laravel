<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\AssetAcquisitionResource;

class ListAssetAcquisitions extends ListRecords
{
    #[\Override]
    protected static string $resource = AssetAcquisitionResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

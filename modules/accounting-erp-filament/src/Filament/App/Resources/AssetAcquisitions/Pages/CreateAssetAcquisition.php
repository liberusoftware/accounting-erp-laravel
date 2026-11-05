<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\AssetAcquisitions\AssetAcquisitionResource;

class CreateAssetAcquisition extends CreateRecord
{
    #[\Override]
    protected static string $resource = AssetAcquisitionResource::class;
}

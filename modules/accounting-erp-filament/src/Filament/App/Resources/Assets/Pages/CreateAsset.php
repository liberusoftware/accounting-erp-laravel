<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Assets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Assets\AssetResource;

class CreateAsset extends CreateRecord
{
    #[\Override]
    protected static string $resource = AssetResource::class;
}

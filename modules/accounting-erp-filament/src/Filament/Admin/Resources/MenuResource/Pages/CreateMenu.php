<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource;

class CreateMenu extends CreateRecord
{
    #[\Override]
    protected static string $resource = MenuResource::class;
}

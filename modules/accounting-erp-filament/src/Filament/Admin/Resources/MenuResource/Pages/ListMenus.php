<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource;

class ListMenus extends ListRecords
{
    #[\Override]
    protected static string $resource = MenuResource::class;
}

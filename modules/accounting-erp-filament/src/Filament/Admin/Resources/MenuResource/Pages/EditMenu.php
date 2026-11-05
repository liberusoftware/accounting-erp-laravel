<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\MenuResource;

class EditMenu extends EditRecord
{
    #[\Override]
    protected static string $resource = MenuResource::class;
}

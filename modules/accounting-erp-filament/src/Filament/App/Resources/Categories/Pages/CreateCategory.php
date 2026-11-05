<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\Categories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Categories\CategoryResource;

class CreateCategory extends CreateRecord
{
    #[\Override]
    protected static string $resource = CategoryResource::class;
}

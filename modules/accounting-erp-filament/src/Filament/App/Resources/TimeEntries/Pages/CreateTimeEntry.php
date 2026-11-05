<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\TimeEntries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\TimeEntries\TimeEntryResource;

class CreateTimeEntry extends CreateRecord
{
    #[\Override]
    protected static string $resource = TimeEntryResource::class;
}

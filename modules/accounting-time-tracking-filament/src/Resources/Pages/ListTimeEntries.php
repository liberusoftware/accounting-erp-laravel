<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\TimeTrackingFilament\Resources\TimeEntryResource;

final class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationFilament\Resources\DepreciationScheduleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\DepreciationFilament\Resources\DepreciationScheduleResource;

final class ListDepreciationSchedules extends ListRecords
{
    protected static string $resource = DepreciationScheduleResource::class;
}

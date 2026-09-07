<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\RevenueRecognitionFilament\Resources\RevenueScheduleResource;

final class ListRevenueSchedules extends ListRecords
{
    protected static string $resource = RevenueScheduleResource::class;
}

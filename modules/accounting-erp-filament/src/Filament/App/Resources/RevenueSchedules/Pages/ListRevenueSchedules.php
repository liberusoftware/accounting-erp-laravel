<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Resources\RevenueSchedules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\RevenueSchedules\RevenueScheduleResource;

class ListRevenueSchedules extends ListRecords
{
    #[\Override]
    protected static string $resource = RevenueScheduleResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

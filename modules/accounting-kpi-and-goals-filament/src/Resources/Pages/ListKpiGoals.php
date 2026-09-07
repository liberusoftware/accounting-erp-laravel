<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\KpiAndGoalsFilament\Resources\KpiGoalResource;

final class ListKpiGoals extends ListRecords
{
    protected static string $resource = KpiGoalResource::class;
}

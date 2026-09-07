<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\WorkforceCostingFilament\Resources\WorkforceCostingRuleResource;

final class ListWorkforceCostingRules extends ListRecords
{
    protected static string $resource = WorkforceCostingRuleResource::class;
}

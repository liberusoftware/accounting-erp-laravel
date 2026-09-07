<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\BudgetsFilament\Resources\BudgetResource;

final class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;
}

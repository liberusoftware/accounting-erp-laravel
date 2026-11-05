<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Budgets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Budgets\BudgetResource;

class CreateBudget extends CreateRecord
{
    #[\Override]
    protected static string $resource = BudgetResource::class;
}

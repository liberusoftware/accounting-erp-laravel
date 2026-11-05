<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Budgets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Budgets\BudgetResource;

class ListBudgets extends ListRecords
{
    #[\Override]
    protected static string $resource = BudgetResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

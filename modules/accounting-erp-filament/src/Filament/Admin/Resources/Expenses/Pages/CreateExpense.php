<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Expenses\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Accounting\ErpFilament\Filament\Admin\Resources\Expenses\ExpenseResource;

class CreateExpense extends CreateRecord
{
    #[\Override]
    protected static string $resource = ExpenseResource::class;
}

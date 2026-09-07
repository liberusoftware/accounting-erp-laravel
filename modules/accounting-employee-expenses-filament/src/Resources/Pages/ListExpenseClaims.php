<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Accounting\EmployeeExpensesFilament\Resources\ExpenseClaimResource;

final class ListExpenseClaims extends ListRecords
{
    protected static string $resource = ExpenseClaimResource::class;
}

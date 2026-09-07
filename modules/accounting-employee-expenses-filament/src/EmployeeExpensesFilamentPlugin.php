<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\EmployeeExpensesFilament\Resources\ExpenseClaimResource;

final class EmployeeExpensesFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-employee-expenses-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ExpenseClaimResource::class]);
    }

    public function boot(Panel $panel): void {}
}

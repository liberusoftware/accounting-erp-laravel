<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\BudgetsFilament\Resources\BudgetResource;

final class BudgetsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'accounting-budgets';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BudgetResource::class]);
    }

    public function boot(Panel $panel): void {}
}

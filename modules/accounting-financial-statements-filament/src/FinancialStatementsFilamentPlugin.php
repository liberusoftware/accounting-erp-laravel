<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Accounting\FinancialStatementsFilament\Pages\FinancialStatements;

final class FinancialStatementsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-financial-statements-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([FinancialStatements::class]);
    }

    public function boot(Panel $panel): void {}
}

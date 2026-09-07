<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecastingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class CashFlowForecastingFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return new self();
    }

    public function getId(): string
    {
        return 'module-accounting-cash-flow-forecasting-filament';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}

<?php

declare(strict_types=1);

use Liberu\Accounting\CashFlowForecastingFilament\CashFlowForecastingFilamentPlugin;

it('exposes the cash-flow forecasting Filament plugin', function (): void {
    expect(app(CashFlowForecastingFilamentPlugin::class)->getId())
        ->toBe('module-accounting-cash-flow-forecasting-filament');
});

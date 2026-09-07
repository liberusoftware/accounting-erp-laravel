<?php

declare(strict_types=1);

use Liberu\Accounting\CashPositionFilament\CashPositionFilamentPlugin;

it('exposes the cash position Filament plugin', function (): void {
    expect(app(CashPositionFilamentPlugin::class)->getId())
        ->toBe('module-accounting-cash-position-filament');
});

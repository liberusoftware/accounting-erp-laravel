<?php

use Liberu\Accounting\TransfersFilament\TransfersFilamentPlugin;

it('exposes the transfers Filament plugin', function (): void {
    expect(TransfersFilamentPlugin::make()->getId())->toBe('module-accounting-transfers-filament');
});

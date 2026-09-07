<?php

use Liberu\Accounting\DepositsAndClearingFilament\DepositsAndClearingFilamentPlugin;

it('exposes the Deposits and Clearing Filament plugin', function (): void {
    expect(DepositsAndClearingFilamentPlugin::make()->getId())->toBe('accounting-deposits-and-clearing');
});

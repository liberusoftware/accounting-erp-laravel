<?php

use Liberu\Accounting\TaxReturnsFilament\TaxReturnsFilamentPlugin;

it('exposes the tax returns Filament plugin', function (): void {
    expect(TaxReturnsFilamentPlugin::make()->getId())->toBe('module-accounting-tax-returns-filament');
});

<?php

use Liberu\Accounting\VatFilament\VatFilamentPlugin;

it('exposes the VAT Filament plugin', function (): void {
    expect(VatFilamentPlugin::make()->getId())->toBe('module-accounting-vat-filament');
});

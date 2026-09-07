<?php

use Liberu\Accounting\WithholdingTaxFilament\WithholdingTaxFilamentPlugin;

it('exposes the withholding tax Filament plugin', function (): void {
    expect(WithholdingTaxFilamentPlugin::make()->getId())->toBe('module-accounting-withholding-tax-filament');
});

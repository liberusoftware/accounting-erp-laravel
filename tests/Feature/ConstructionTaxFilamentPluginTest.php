<?php

declare(strict_types=1);

use Liberu\Accounting\ConstructionTaxFilament\ConstructionTaxFilamentPlugin;

it('exposes the construction tax Filament plugin', function (): void {
    expect(app(ConstructionTaxFilamentPlugin::class)->getId())
        ->toBe('module-accounting-construction-tax-filament');
});

<?php

use Liberu\Accounting\ProjectProfitabilityFilament\ProjectProfitabilityFilamentPlugin;

it('discovers the project profitability Filament plugin on the app panel', function (): void {
    expect(app(ProjectProfitabilityFilamentPlugin::class)->getId())->toBe('module-accounting-project-profitability-filament');
});

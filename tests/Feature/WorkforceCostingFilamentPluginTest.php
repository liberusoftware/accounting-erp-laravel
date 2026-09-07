<?php

use Liberu\Accounting\WorkforceCostingFilament\WorkforceCostingFilamentPlugin;

it('exposes the Workforce Costing Filament plugin', function (): void {
    expect(WorkforceCostingFilamentPlugin::make()->getId())->toBe('module-accounting-workforce-costing-filament');
});

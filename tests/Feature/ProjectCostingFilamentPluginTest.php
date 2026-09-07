<?php

use Liberu\Accounting\ProjectCostingFilament\ProjectCostingFilamentPlugin;

it('discovers the project costing Filament plugin on the app panel', function (): void {
    expect(app(ProjectCostingFilamentPlugin::class)->getId())->toBe('module-accounting-project-costing-filament');
});

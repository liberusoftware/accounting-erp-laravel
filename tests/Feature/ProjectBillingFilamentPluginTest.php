<?php

use Liberu\Accounting\ProjectBillingFilament\ProjectBillingFilamentPlugin;

it('discovers the project billing Filament plugin on the app panel', function (): void {
    expect(app(ProjectBillingFilamentPlugin::class)->getId())->toBe('module-accounting-project-billing-filament');
});

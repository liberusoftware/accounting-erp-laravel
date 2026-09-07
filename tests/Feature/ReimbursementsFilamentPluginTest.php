<?php

use Liberu\Accounting\ReimbursementsFilament\ReimbursementsFilamentPlugin;

it('discovers the reimbursements Filament plugin on the app panel', function (): void {
    expect(app(ReimbursementsFilamentPlugin::class)->getId())->toBe('module-accounting-reimbursements-filament');
});

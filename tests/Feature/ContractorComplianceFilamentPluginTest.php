<?php

declare(strict_types=1);

use Liberu\Accounting\ContractorComplianceFilament\ContractorComplianceFilamentPlugin;

it('exposes the contractor compliance Filament plugin', function (): void {
    expect(app(ContractorComplianceFilamentPlugin::class)->getId())
        ->toBe('module-accounting-contractor-compliance-filament');
});

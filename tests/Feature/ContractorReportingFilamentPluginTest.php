<?php

declare(strict_types=1);

use Liberu\Accounting\ContractorReportingFilament\ContractorReportingFilamentPlugin;

it('exposes the contractor reporting Filament plugin', function (): void {
    expect(app(ContractorReportingFilamentPlugin::class)->getId())
        ->toBe('module-accounting-contractor-reporting-filament');
});

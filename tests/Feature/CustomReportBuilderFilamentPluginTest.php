<?php

declare(strict_types=1);

use Liberu\Accounting\CustomReportBuilderFilament\CustomReportBuilderFilamentPlugin;

it('exposes the Custom Report Builder Filament plugin', function (): void {
    expect(CustomReportBuilderFilamentPlugin::make()->getId())->toBe('module-accounting-custom-report-builder-filament');
});

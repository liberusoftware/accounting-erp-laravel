<?php

use Liberu\Accounting\XeroMigrationFilament\XeroMigrationFilamentPlugin;

it('exposes the Xero migration Filament plugin', function (): void {
    expect(XeroMigrationFilamentPlugin::make()->getId())->toBe('module-accounting-xero-migration-filament');
});

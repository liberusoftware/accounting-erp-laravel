<?php

declare(strict_types=1);

use Liberu\Accounting\CloseManagementFilament\CloseManagementFilamentPlugin;

it('exposes the close management Filament plugin', function (): void {
    expect(app(CloseManagementFilamentPlugin::class)->getId())
        ->toBe('module-accounting-close-management-filament');
});

<?php

declare(strict_types=1);

use Liberu\Accounting\CustomerPortalFilament\CustomerPortalFilamentPlugin;

it('exposes the Customer Portal Filament plugin', function (): void {
    expect(CustomerPortalFilamentPlugin::make()->getId())->toBe('module-accounting-customer-portal-filament');
});

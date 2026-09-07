<?php

declare(strict_types=1);

use Liberu\Accounting\DashboardsFilament\DashboardsFilamentPlugin;

it('exposes the Dashboards Filament plugin', function (): void {
    expect(DashboardsFilamentPlugin::make()->getId())->toBe('module-accounting-dashboards-filament');
});

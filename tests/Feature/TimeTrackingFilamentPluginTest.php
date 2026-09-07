<?php

use Liberu\Accounting\TimeTrackingFilament\TimeTrackingFilamentPlugin;

it('exposes the time tracking Filament plugin', function (): void {
    expect(TimeTrackingFilamentPlugin::make()->getId())->toBe('module-accounting-time-tracking-filament');
});

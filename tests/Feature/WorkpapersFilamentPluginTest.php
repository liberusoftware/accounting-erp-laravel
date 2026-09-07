<?php

use Liberu\Accounting\WorkpapersFilament\WorkpapersFilamentPlugin;

it('exposes the Workpapers Filament plugin', function (): void {
    expect(WorkpapersFilamentPlugin::make()->getId())->toBe('module-accounting-workpapers-filament');
});

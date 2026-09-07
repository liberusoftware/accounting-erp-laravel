<?php

declare(strict_types=1);

use Liberu\Accounting\YearEndFilament\YearEndFilamentPlugin;

it('exposes the Year End Filament plugin', function (): void {
    expect(YearEndFilamentPlugin::make()->getId())->toBe('module-accounting-year-end-filament');
});

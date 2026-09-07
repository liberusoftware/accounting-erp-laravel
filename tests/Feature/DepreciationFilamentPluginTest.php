<?php

declare(strict_types=1);

use Liberu\Accounting\DepreciationFilament\DepreciationFilamentPlugin;

it('exposes the Depreciation Filament plugin', function (): void {
    expect(DepreciationFilamentPlugin::make()->getId())->toBe('accounting-depreciation');
});

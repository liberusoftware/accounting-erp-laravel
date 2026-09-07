<?php

use Liberu\Accounting\RevenueRecognitionFilament\RevenueRecognitionFilamentPlugin;

it('discovers the revenue recognition Filament plugin on the app panel', function (): void {
    expect(app(RevenueRecognitionFilamentPlugin::class)->getId())->toBe('module-accounting-revenue-recognition-filament');
});

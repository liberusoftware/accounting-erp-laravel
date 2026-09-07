<?php

declare(strict_types=1);

use Liberu\Accounting\ConsolidationFilament\ConsolidationFilamentPlugin;

it('exposes the consolidation Filament plugin', function (): void {
    expect(app(ConsolidationFilamentPlugin::class)->getId())
        ->toBe('module-accounting-consolidation-filament');
});

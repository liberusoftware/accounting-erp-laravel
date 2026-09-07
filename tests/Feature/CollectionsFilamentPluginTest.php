<?php

declare(strict_types=1);

use Liberu\Accounting\CollectionsFilament\CollectionsFilamentPlugin;

it('exposes the collections Filament plugin', function (): void {
    expect(app(CollectionsFilamentPlugin::class)->getId())
        ->toBe('module-accounting-collections-filament');
});

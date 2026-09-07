<?php

declare(strict_types=1);

use Liberu\Accounting\CodingSuggestionsFilament\CodingSuggestionsFilamentPlugin;

it('exposes the coding suggestions Filament plugin', function (): void {
    expect(app(CodingSuggestionsFilamentPlugin::class)->getId())
        ->toBe('module-accounting-coding-suggestions-filament');
});

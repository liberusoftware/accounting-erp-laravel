<?php

declare(strict_types=1);

use Liberu\Accounting\CreditNotesAndAdjustmentsFilament\CreditNotesAndAdjustmentsFilamentPlugin;

it('exposes the Credit Notes and Adjustments Filament plugin', function (): void {
    expect(CreditNotesAndAdjustmentsFilamentPlugin::make()->getId())->toBe('module-accounting-credit-notes-and-adjustments-filament');
});

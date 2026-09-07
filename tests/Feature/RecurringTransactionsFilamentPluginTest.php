<?php

use Liberu\Accounting\RecurringTransactionsFilament\RecurringTransactionsFilamentPlugin;

it('discovers the recurring transactions Filament plugin on the app panel', function (): void {
    expect(app(RecurringTransactionsFilamentPlugin::class)->getId())->toBe('module-accounting-recurring-transactions-filament');
});

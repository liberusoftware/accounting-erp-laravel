<?php

use Liberu\Accounting\ReceiptManagementFilament\ReceiptManagementFilamentPlugin;

it('discovers the receipt management Filament plugin on the app panel', function (): void {
    expect(app(ReceiptManagementFilamentPlugin::class)->getId())->toBe('module-accounting-receipt-management-filament');
});

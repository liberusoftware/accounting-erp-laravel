<?php

declare(strict_types=1);

use Liberu\Accounting\CustomerPaymentsFilament\CustomerPaymentsFilamentPlugin;

it('exposes the Customer Payments Filament plugin', function (): void {
    expect(CustomerPaymentsFilamentPlugin::make()->getId())->toBe('module-accounting-customer-payments-filament');
});

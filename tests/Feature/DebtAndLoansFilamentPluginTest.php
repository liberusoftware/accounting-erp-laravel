<?php

declare(strict_types=1);

use Liberu\Accounting\DebtAndLoansFilament\DebtAndLoansFilamentPlugin;

it('exposes the Debt and Loans Filament plugin', function (): void {
    expect(DebtAndLoansFilamentPlugin::make()->getId())->toBe('accounting-debt-and-loans');
});

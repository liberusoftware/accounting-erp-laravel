<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CashPosition\Actions\CreateCashPosition;
use Liberu\Accounting\CashPosition\Actions\RefreshCashPosition;
use Liberu\Accounting\CashPosition\Exceptions\InvalidCashPosition;

uses(RefreshDatabase::class);

it('records and refreshes a cash position snapshot', function (): void {
    $position = app(CreateCashPosition::class)->handle(['team_id' => 909, 'view_ref' => 'treasury', 'entity_ref' => 'entity-1', 'currency' => 'GBP', 'ledger_balance' => 1000, 'available_balance' => 800, 'outstanding_receipts' => 200]);
    $refreshed = app(RefreshCashPosition::class)->handle($position, ['available_balance' => 900, 'committed_cash' => 150]);

    expect((float) $refreshed->available_balance)->toBe(900.0)
        ->and((float) $refreshed->committed_cash)->toBe(150.0)
        ->and($refreshed->refreshed_at)->not->toBeNull();
});

it('rejects invalid cash position currency and balances', function (): void {
    expect(fn () => app(CreateCashPosition::class)->handle(['team_id' => 909, 'view_ref' => 'bad', 'currency' => 'gbp', 'ledger_balance' => 1]))
        ->toThrow(InvalidCashPosition::class);
});

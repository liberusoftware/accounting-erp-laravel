<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Transfers\Actions\CompleteTransfer;
use Liberu\Accounting\Transfers\Actions\CreateTransfer;
use Liberu\Accounting\Transfers\Actions\ReconcileTransfer;
use Liberu\Accounting\Transfers\Enums\TransferStatus;
use Liberu\Accounting\Transfers\Exceptions\InvalidTransfer;

uses(RefreshDatabase::class);

it('completes and reconciles an in-transit transfer', function (): void {
    $transfer = app(CreateTransfer::class)->handle([
        'team_id' => 7,
        'source_account_ref' => 'bank-ca',
        'destination_account_ref' => 'bank-us',
        'source_currency' => 'CAD',
        'destination_currency' => 'USD',
        'source_amount' => 100,
        'exchange_rate' => 0.74,
        'fee_amount' => 2,
    ]);

    expect((float) $transfer->destination_amount)->toBe(74.0)
        ->and($transfer->status)->toBe(TransferStatus::InTransit);

    app(CompleteTransfer::class)->handle($transfer);
    $reconciliation = app(ReconcileTransfer::class)->handle($transfer->fresh(), [
        'external_ref' => 'bank-ref-1',
        'amount' => 74,
        'reconciled_on' => '2026-08-29',
    ]);

    expect($reconciliation->team_id)->toBe(7)
        ->and($transfer->fresh()->status)->toBe(TransferStatus::Reconciled);
});

it('rejects transfers between the same account', function (): void {
    expect(fn (): mixed => app(CreateTransfer::class)->handle([
        'team_id' => 7,
        'source_account_ref' => 'bank-ca',
        'destination_account_ref' => 'bank-ca',
        'source_currency' => 'CAD',
        'destination_currency' => 'CAD',
        'source_amount' => 100,
        'exchange_rate' => 1,
    ]))->toThrow(InvalidTransfer::class);
});

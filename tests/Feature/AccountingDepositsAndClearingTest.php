<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\DepositsAndClearing\Actions\CreateGroupedDeposit;
use Liberu\Accounting\DepositsAndClearing\Actions\ReconcileDeposit;
use Liberu\Accounting\DepositsAndClearing\Actions\RecordUndepositedFund;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingDepositStatus;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingFundStatus;
use Liberu\Accounting\DepositsAndClearing\Exceptions\InvalidClearing;

uses(RefreshDatabase::class);

it('groups undeposited funds and reconciles provider fees and payouts', function (): void {
    $record = fn (string $source, float $amount) => app(RecordUndepositedFund::class)->handle(['team_id' => 12, 'source_type' => 'payment', 'source_id' => $source, 'amount' => $amount, 'currency' => 'GBP', 'received_on' => '2026-08-29']);
    $first = $record('pay-1', 100);
    $second = $record('pay-2', 50);
    $deposit = app(CreateGroupedDeposit::class)->handle(['team_id' => 12, 'deposit_ref' => 'DEP-1', 'account_ref' => 'bank-1', 'currency' => 'GBP', 'deposit_date' => '2026-08-29', 'provider' => 'stripe'], [$first->id, $second->id]);

    expect($deposit->gross_amount)->toBe('150.00')->and($first->refresh()->status)->toBe(ClearingFundStatus::Grouped);
    $reconciled = app(ReconcileDeposit::class)->handle($deposit, 147, 3);
    expect($reconciled->status)->toBe(ClearingDepositStatus::Reconciled)->and($reconciled->fee_amount)->toBe('3.00')->and($second->refresh()->status)->toBe(ClearingFundStatus::Reconciled);
});

it('rejects invalid amounts and unbalanced reconciliation', function (): void {
    expect(fn () => app(RecordUndepositedFund::class)->handle(['team_id' => 1, 'source_type' => 'payment', 'source_id' => 'bad', 'amount' => 0, 'currency' => 'GBP', 'received_on' => '2026-08-29']))->toThrow(InvalidClearing::class);
});

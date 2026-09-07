<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CorporateCards\Actions\ApproveCardTransaction;
use Liberu\Accounting\CorporateCards\Actions\CodeCardTransaction;
use Liberu\Accounting\CorporateCards\Actions\CreateCardAccount;
use Liberu\Accounting\CorporateCards\Actions\ReconcileCardTransaction;
use Liberu\Accounting\CorporateCards\Actions\RecordCardTransaction;
use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;
use Liberu\Accounting\CorporateCards\Exceptions\InvalidCorporateCard;

uses(RefreshDatabase::class);

it('codes, approves and reconciles a card transaction', function (): void {
    $account = app(CreateCardAccount::class)->handle(['team_id' => 91, 'card_ref' => 'CARD-1', 'holder_ref' => 'user-1', 'currency' => 'GBP', 'limit_amount' => 1000, 'controls' => ['merchant_categories' => ['travel']]]);
    $transaction = app(RecordCardTransaction::class)->handle($account, ['transaction_ref' => 'TX-1', 'transaction_date' => '2026-08-29', 'amount' => 250, 'currency' => 'GBP', 'merchant_ref' => 'hotel']);
    app(CodeCardTransaction::class)->handle($transaction, 'travel', 'receipt-1');
    app(ApproveCardTransaction::class)->handle($transaction->refresh(), 4);
    $reconciled = app(ReconcileCardTransaction::class)->handle($transaction->refresh(), 'BANK-1');
    expect($reconciled->status)->toBe(CardTransactionStatus::Reconciled)->and((float) $account->refresh()->spent_amount)->toBe(250.0);
});

it('rejects transactions beyond the card limit', function (): void {
    $account = app(CreateCardAccount::class)->handle(['team_id' => 91, 'card_ref' => 'CARD-2', 'holder_ref' => 'user-1', 'currency' => 'GBP', 'limit_amount' => 100]);
    expect(fn () => app(RecordCardTransaction::class)->handle($account, ['transaction_ref' => 'TX-2', 'transaction_date' => '2026-08-29', 'amount' => 101, 'currency' => 'GBP']))->toThrow(InvalidCorporateCard::class);
});

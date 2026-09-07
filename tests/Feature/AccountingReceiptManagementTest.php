<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ReceiptManagement\Actions\IngestReceipt;
use Liberu\Accounting\ReceiptManagement\Actions\MatchReceipt;
use Liberu\Accounting\ReceiptManagement\Actions\RequestMissingReceipt;
use Liberu\Accounting\ReceiptManagement\Enums\ReceiptStatus;
use Liberu\Accounting\ReceiptManagement\Exceptions\InvalidReceipt;

uses(RefreshDatabase::class);
it('ingests idempotently, matches to opaque targets, and audits the receipt', function (): void {
    $receipt = app(IngestReceipt::class)->handle(['file_ref' => 'media://receipt-1', 'merchant' => 'Acme', 'amount' => 25, 'currency' => 'USD']);
    expect(app(IngestReceipt::class)->handle(['file_ref' => 'media://receipt-1'])->id)->toBe($receipt->id);
    app(MatchReceipt::class)->handle($receipt, ['target_type' => 'expense', 'target_id' => 'EXP-1', 'actor_ref' => 'user-1']);
    expect($receipt->refresh()->status)->toBe(ReceiptStatus::Matched)->and($receipt->matches)->toHaveCount(1)->and($receipt->annotations)->toHaveCount(0);
});
it('creates missing receipt requests and rejects invalid or purged matching', function (): void {
    $receipt = app(IngestReceipt::class)->handle(['file_ref' => 'media://receipt-2']);
    $request = app(RequestMissingReceipt::class)->handle(['receipt_id' => $receipt->id, 'requestee_ref' => 'employee-1', 'target_type' => 'card', 'target_id' => 'CARD-1', 'reason' => 'Receipt required']);
    expect($request->status)->toBe('open')->and($receipt->refresh()->status)->toBe(ReceiptStatus::Requested);
    expect(fn () => app(IngestReceipt::class)->handle([]))->toThrow(InvalidReceipt::class);
    $receipt->update(['status' => ReceiptStatus::Purged]);
    expect(fn () => app(MatchReceipt::class)->handle($receipt, ['target_type' => 'bill', 'target_id' => 'B-1']))->toThrow(InvalidReceipt::class);
});

<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CashCoding\Actions\CreateCashCodingBatch;
use Liberu\Accounting\CashCoding\Actions\TransitionCashCodingBatch;
use Liberu\Accounting\CashCoding\Enums\CashCodingStatus;
use Liberu\Accounting\CashCoding\Exceptions\InvalidCashCoding;

uses(RefreshDatabase::class);

function cashCodingLines(): array
{
    return [['source_reference' => 'txn-1', 'amount' => -100, 'currency' => 'GBP', 'account_id' => '6000', 'tax_code' => 'standard', 'dimension_id' => 'dept-1']];
}

it('reviews, posts and undoes a cash coding batch', function (): void {
    $batch = app(CreateCashCodingBatch::class)->handle(['team_id' => 91, 'reference' => 'CASH-1', 'currency' => 'gbp', 'lines' => cashCodingLines(), 'created_by' => 1]);
    $action = app(TransitionCashCodingBatch::class);
    $action->review($batch, 2);
    $action->post($batch->refresh(), 2);
    $undone = $action->undo($batch->refresh(), 'Correction required');

    expect($undone->status)->toBe(CashCodingStatus::Undone)->and((float) $undone->total_amount)->toBe(-100.0)->and($undone->undo_reason)->toBe('Correction required');
});

it('enforces line limits, duplicate references and lifecycle transitions', function (): void {
    $input = ['team_id' => 91, 'reference' => 'CASH-2', 'currency' => 'GBP', 'lines' => cashCodingLines()];
    app(CreateCashCodingBatch::class)->handle($input);
    expect(fn () => app(CreateCashCodingBatch::class)->handle($input))->toThrow(InvalidCashCoding::class);
    expect(fn () => app(TransitionCashCodingBatch::class)->post(app(CreateCashCodingBatch::class)->handle(['team_id' => 91, 'reference' => 'CASH-3', 'currency' => 'GBP', 'lines' => cashCodingLines()])))->toThrow(InvalidCashCoding::class);
});

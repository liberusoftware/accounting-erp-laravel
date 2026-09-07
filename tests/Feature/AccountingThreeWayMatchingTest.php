<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ThreeWayMatching\Actions\ApproveMatch;
use Liberu\Accounting\ThreeWayMatching\Actions\CaptureMatchEvidence;
use Liberu\Accounting\ThreeWayMatching\Actions\EvaluateMatch;
use Liberu\Accounting\ThreeWayMatching\Actions\ResolveMatchException;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionStatus;
use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;
use Liberu\Accounting\ThreeWayMatching\Exceptions\InvalidMatch;

uses(RefreshDatabase::class);

it('evaluates and approves an exact three-way match', function (): void {
    $match = app(EvaluateMatch::class)->handle(threeWayMatchAttributes());

    expect($match->status)->toBe(MatchStatus::Matched)
        ->and($match->exceptions)->toHaveCount(0)
        ->and(app(ApproveMatch::class)->handle($match, 7)->status)->toBe(MatchStatus::Approved);
});

it('records a partial quantity match as a warning', function (): void {
    $attributes = threeWayMatchAttributes();
    $attributes['billed_quantity'] = 8;

    $match = app(EvaluateMatch::class)->handle($attributes);

    expect($match->status)->toBe(MatchStatus::Partial)
        ->and($match->exceptions)->toHaveCount(1)
        ->and($match->exceptions->first()->kind)->toBe('partial_quantity')
        ->and($match->exceptions->first()->severity->value)->toBe('warning');
});

it('requires an override for blocking variances and supports resolution', function (): void {
    $attributes = threeWayMatchAttributes();
    $attributes['billed_unit_price'] = 125;

    $match = app(EvaluateMatch::class)->handle($attributes);
    $exception = $match->exceptions->firstOrFail();

    expect($match->status)->toBe(MatchStatus::Exception)
        ->and(fn () => app(ApproveMatch::class)->handle($match, 7))->toThrow(InvalidMatch::class);

    app(ResolveMatchException::class)->handle($exception, 7, 'Approved price adjustment.');

    expect($match->fresh()->status)->toBe(MatchStatus::Matched)
        ->and($exception->fresh()->status)->toBe(ExceptionStatus::Resolved)
        ->and(app(ApproveMatch::class)->handle($match->fresh(), 7)->status)->toBe(MatchStatus::Approved);
});

it('is idempotent for the same source references and deduplicates evidence', function (): void {
    $attributes = threeWayMatchAttributes();
    $first = app(EvaluateMatch::class)->handle($attributes);
    $second = app(EvaluateMatch::class)->handle($attributes);

    app(CaptureMatchEvidence::class)->handle($first, 'supplier-bill', 'bill-1', ['total' => 100], 7);
    app(CaptureMatchEvidence::class)->handle($first->fresh(), 'supplier-bill', 'bill-1', ['total' => 100], 7);

    expect($second->id)->toBe($first->id)
        ->and($first->refresh()->evidence)->toHaveCount(1)
        ->and($first->refresh()->evidence->firstOrFail()->snapshot_hash)->toHaveLength(64);
});

/** @return array<string, mixed> */
function threeWayMatchAttributes(): array
{
    return [
        'purchase_order_type' => 'purchase_order',
        'purchase_order_id' => 'po-1',
        'receipt_type' => 'receipt',
        'receipt_id' => 'receipt-1',
        'bill_type' => 'supplier_bill',
        'bill_id' => 'bill-1',
        'currency' => 'USD',
        'ordered_quantity' => 10,
        'received_quantity' => 10,
        'billed_quantity' => 10,
        'ordered_unit_price' => 100,
        'billed_unit_price' => 100,
        'expected_tax' => 20,
        'billed_tax' => 20,
    ];
}

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PaymentReconciliation\Actions\IdentifyMissingItems;
use Liberu\Accounting\PaymentReconciliation\Actions\ImportSettlement;
use Liberu\Accounting\PaymentReconciliation\Actions\MatchSettlementItem;
use Liberu\Accounting\PaymentReconciliation\Actions\RecordProviderDrift;
use Liberu\Accounting\PaymentReconciliation\Actions\ResolveReconciliationException;
use Liberu\Accounting\PaymentReconciliation\Enums\ReconciliationExceptionStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementStatus;
use Liberu\Accounting\PaymentReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\PaymentReconciliation\Queries\SettlementQuery;

uses(RefreshDatabase::class);

function paymentSettlementAttributes(): array
{
    return ['team_id' => 1, 'provider' => 'gateway-a', 'merchant_ref' => 'merchant-1', 'settlement_ref' => 'SET-100', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP', 'items' => []];
}

function paymentSettlementItems(): array
{
    return [
        ['external_ref' => 'charge-1', 'type' => 'charge', 'gross_amount' => 100, 'fee_amount' => 3, 'refund_amount' => 0, 'dispute_amount' => 0, 'net_amount' => 97, 'source_payload' => ['provider_id' => 'charge-1']],
        ['external_ref' => 'refund-1', 'type' => 'refund', 'gross_amount' => 0, 'fee_amount' => 0, 'refund_amount' => 10, 'dispute_amount' => 0, 'net_amount' => 10],
    ];
}

it('imports settlements idempotently and calculates gross to net totals', function (): void {
    $action = app(ImportSettlement::class);
    $run = $action->handle(paymentSettlementAttributes(), paymentSettlementItems());
    $same = $action->handle(paymentSettlementAttributes(), paymentSettlementItems());

    expect($same->id)->toBe($run->id)
        ->and($run->status)->toBe(SettlementStatus::Imported)
        ->and((float) $run->gross_amount)->toBe(100.0)
        ->and((float) $run->fee_amount)->toBe(3.0)
        ->and((float) $run->net_amount)->toBe(107.0)
        ->and($run->items)->toHaveCount(2)
        ->and($run->audits)->toHaveCount(1);

    expect(fn (): mixed => $action->handle(paymentSettlementAttributes(), array_merge(paymentSettlementItems(), [['external_ref' => 'different', 'type' => 'charge', 'gross_amount' => 1, 'net_amount' => 1]])))
        ->toThrow(InvalidReconciliation::class);
});

it('matches items, identifies missing evidence, records drift, and resolves exceptions', function (): void {
    $run = app(ImportSettlement::class)->handle(paymentSettlementAttributes(), paymentSettlementItems());
    $item = $run->items()->where('external_ref', 'charge-1')->firstOrFail();
    $match = app(MatchSettlementItem::class)->handle($item, 'invoice', 'INV-1', 97, 7, 'match-1');

    expect($match->matched_amount)->toEqual('97.00')
        ->and($item->refresh()->status)->toBe(SettlementItemStatus::Matched)
        ->and($run->refresh()->status)->toBe(SettlementStatus::PartiallyMatched);

    $missing = app(IdentifyMissingItems::class)->handle($run->refresh(), [['external_ref' => 'charge-missing', 'expected_amount' => 20, 'currency' => 'GBP']]);
    expect($missing)->toHaveCount(1)->and($run->refresh()->status)->toBe(SettlementStatus::Exception);

    $drift = app(RecordProviderDrift::class)->handle($run->refresh(), 'net_amount', '107.00', '106.00', 'blocking');
    expect($drift->status->value)->toBe('open');
    $exception = $run->exceptions()->where('kind', 'missing_item')->firstOrFail();
    $resolved = app(ResolveReconciliationException::class)->handle($exception, 7, 'Provider replay supplied the missing item.');
    expect($resolved->status)->toBe(ReconciliationExceptionStatus::Resolved);
});

it('keeps summaries tenant scoped and rejects unsafe matches', function (): void {
    $run = app(ImportSettlement::class)->handle(paymentSettlementAttributes(), paymentSettlementItems());
    $item = $run->items()->firstOrFail();
    expect(fn (): mixed => app(MatchSettlementItem::class)->handle($item, 'invoice', 'INV-2', 1000))
        ->toThrow(InvalidReconciliation::class);

    expect(app(SettlementQuery::class)->summary(1)['count'])->toBe(1)
        ->and(app(SettlementQuery::class)->summary(2)['count'])->toBe(0);
});

it('exposes the one-to-one authenticated API contract', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.payment-reconciliation.read', 'accounting.payment-reconciliation.write']);

    $payload = paymentSettlementAttributes();
    $payload['items'] = paymentSettlementItems();

    $created = $this->postJson('/api/v1/accounting/payment-reconciliation', $payload)
        ->assertCreated()
        ->assertJsonPath('data.type', 'accounting-payment-reconciliation')
        ->assertJsonPath('data.attributes.provider', 'gateway-a')
        ->json('data');

    $this->getJson('/api/v1/accounting/payment-reconciliation')
        ->assertOk()
        ->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/payment-reconciliation/'.$created['id'])
        ->assertOk()
        ->assertJsonPath('data.attributes.settlement_ref', 'SET-100');
    $this->getJson('/api/v1/accounting/payment-reconciliation/summary')
        ->assertOk()
        ->assertJsonPath('count', 1);
});

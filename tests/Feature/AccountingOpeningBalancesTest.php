<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\OpeningBalances\Actions\ApproveOpeningBalances;
use Liberu\Accounting\OpeningBalances\Actions\CreateOpeningBalanceBatch;
use Liberu\Accounting\OpeningBalances\Actions\ReconcileOpeningBalances;
use Liberu\Accounting\OpeningBalances\Actions\ValidateOpeningBalances;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;
use Liberu\Accounting\OpeningBalances\Exceptions\InvalidOpeningBalance;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

uses(RefreshDatabase::class);

function openingBalanceEntries(): array
{
    return [
        ['balance_type' => 'account', 'reference_id' => '1100', 'debit_amount' => 100, 'currency' => 'GBP'],
        ['balance_type' => 'customer', 'reference_id' => 'customer-1', 'document_ref' => 'INV-OPEN-1', 'debit_amount' => 100, 'currency' => 'GBP'],
        ['balance_type' => 'account', 'reference_id' => '1200', 'credit_amount' => 200, 'currency' => 'GBP'],
    ];
}

it('imports, validates, approves, and reconciles opening balances idempotently', function (): void {
    $attributes = ['team_id' => 1, 'batch_ref' => 'OPEN-2026-01', 'migration_date' => '2026-01-01', 'currency' => 'GBP'];
    $create = app(CreateOpeningBalanceBatch::class);
    $batch = $create->handle($attributes, openingBalanceEntries());
    $same = $create->handle($attributes, openingBalanceEntries());
    expect($same->id)->toBe($batch->id)->and($batch->status)->toBe(OpeningBalanceStatus::Draft);

    $batch = app(ValidateOpeningBalances::class)->handle($batch);
    expect($batch->status)->toBe(OpeningBalanceStatus::Validated)->and($batch->entries->every(fn ($entry): bool => $entry->status->value === 'valid'))->toBeTrue();
    $batch = app(ApproveOpeningBalances::class)->handle($batch, 7);
    expect($batch->status)->toBe(OpeningBalanceStatus::Approved);
    $batch = app(ReconcileOpeningBalances::class)->handle($batch, $batch->entries->map(fn ($entry): array => ['entry_id' => $entry->id, 'actual_amount' => $entry->netAmount()])->all());
    expect($batch->status)->toBe(OpeningBalanceStatus::Reconciled)->and($batch->reconciliations)->toHaveCount(3);
});

it('rejects unbalanced and invalid outstanding-document imports', function (): void {
    $draft = app(CreateOpeningBalanceBatch::class)->handle(['batch_ref' => 'BAD', 'migration_date' => '2026-01-01'], [['balance_type' => 'account', 'reference_id' => '1000', 'debit_amount' => 10]]);
    expect(fn (): mixed => app(ValidateOpeningBalances::class)->handle($draft))->toThrow(InvalidOpeningBalance::class);
    expect(fn (): mixed => app(CreateOpeningBalanceBatch::class)->handle(['batch_ref' => 'BAD-DOC', 'migration_date' => '2026-01-01'], [['balance_type' => 'customer', 'reference_id' => 'customer-1', 'debit_amount' => 10]]))->toThrow(InvalidOpeningBalance::class);
    $batch = app(CreateOpeningBalanceBatch::class)->handle(['batch_ref' => 'UNBALANCED', 'migration_date' => '2026-01-01'], [['balance_type' => 'account', 'reference_id' => '1000', 'debit_amount' => 10], ['balance_type' => 'account', 'reference_id' => '2000', 'credit_amount' => 5]]);
    expect(fn (): mixed => app(ValidateOpeningBalances::class)->handle($batch))->toThrow(InvalidOpeningBalance::class);
    expect($batch->refresh()->status)->toBe(OpeningBalanceStatus::Failed);
});

it('registers the authenticated opening-balances API', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.opening-balances.read']);
    $batch = OpeningBalanceBatch::query()->create(['team_id' => 1, 'batch_ref' => 'API', 'migration_date' => '2026-01-01', 'currency' => 'GBP', 'status' => OpeningBalanceStatus::Draft, 'source_hash' => str_repeat('a', 64)]);
    $this->getJson('/api/v1/accounting/opening-balances')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/opening-balances/'.$batch->id)->assertOk()->assertJsonPath('data.attributes.batch_ref', 'API');
});

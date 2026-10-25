<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Transaction;
use App\Services\ProviderTransactionImporter;
use Liberu\Foundation\Organizations\Models\Team;

it('imports provider transactions idempotently without losing reconciliation', function (): void {
    $importer = app(ProviderTransactionImporter::class);
    $row = ['external_id' => 'xero:bank:import-1', 'date' => '2026-10-25', 'description' => 'Supplies', 'amount' => -25.50, 'account_id' => null, 'type' => 'spend'];
    expect($importer->handle([$row], null, fn ($item) => $item))->toBe(1);
    Transaction::query()->where('external_id', $row['external_id'])->firstOrFail()->update(['reconciled' => true]);
    $row['description'] = 'Updated supplies';
    $importer->handle([$row], null, fn ($item) => $item);
    expect(Transaction::query()->where('external_id', $row['external_id'])->count())->toBe(1);
    $this->assertDatabaseHas('transactions', ['external_id' => $row['external_id'], 'amount' => -25.50, 'description' => 'Updated supplies', 'reconciled' => true]);
});

it('keeps provider account lookup within the connection team', function (): void {
    $team = Team::factory()->create();
    $account = Account::factory()->create(['team_id' => $team->getKey(), 'xero_id' => 'private-account']);
    $importer = app(ProviderTransactionImporter::class);
    expect($importer->accountId('xero_id', 'private-account'))->toBeNull()
        ->and($importer->accountId('xero_id', 'private-account', $team->getKey()))->toBe($account->getKey());
});

it('rejects a provider transaction already owned by another team', function (): void {
    $team = Team::factory()->create();
    $importer = app(ProviderTransactionImporter::class);
    $row = ['external_id' => 'qbo:Purchase:private', 'date' => '2026-10-25', 'description' => 'Private', 'amount' => -10, 'account_id' => null, 'type' => 'purchase'];
    $importer->handle([$row], $team->getKey(), fn ($item) => $item);
    expect(fn () => $importer->handle([$row], null, fn ($item) => $item))->toThrow(LogicException::class);
    $this->assertDatabaseHas('transactions', ['external_id' => $row['external_id'], 'team_id' => $team->getKey()]);
});

it('validates provider identifiers before importing or querying accounts', function (): void {
    $importer = app(ProviderTransactionImporter::class);
    expect(fn () => $importer->handle([[]], null, fn () => ['external_id' => '']))->toThrow(InvalidArgumentException::class);
    expect(fn () => $importer->accountId('name', 'untrusted'))->toThrow(InvalidArgumentException::class);
    expect($importer->accountId('qbo_id', null))->toBeNull()
        ->and($importer->accountId('qbo_id', ''))->toBeNull()
        ->and($importer->accountId('qbo_id', 'missing'))->toBeNull()
        ->and($importer->handle([], null, fn () => []))->toBe(0);
    expect(Transaction::query()->count())->toBe(0);
});

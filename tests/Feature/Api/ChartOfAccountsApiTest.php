<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\ChartOfAccountsApi\ChartOfAccountsApiServiceProvider;
use Liberu\Accounting\Core\Models\LegalEntity;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(ChartOfAccountsApiServiceProvider::class, force: true);
});

it('creates, updates, lists, and archives scoped chart accounts', function (): void {
    $user = User::factory()->create();
    $entity = LegalEntity::query()->create([
        'name' => 'Chart Entity',
        'currency_code' => 'GBP',
        'accounting_basis' => 'accrual',
    ]);
    Sanctum::actingAs($user, ['accounting.chart.write']);

    $response = $this->postJson('/api/v1/accounting/chart-of-accounts/accounts', [
        'legal_entity_id' => $entity->id,
        'code' => '1000',
        'name' => 'Cash',
        'type' => 'asset',
    ])->assertCreated()
        ->assertJsonPath('data.attributes.normal_balance', 'debit');

    $account = Account::query()->firstOrFail();
    $this->patchJson("/api/v1/accounting/chart-of-accounts/accounts/{$account->id}", [
        'name' => 'Operating Cash',
    ])->assertOk()->assertJsonPath('data.attributes.name', 'Operating Cash');

    Sanctum::actingAs($user, ['accounting.chart.read']);
    $this->getJson('/api/v1/accounting/chart-of-accounts/accounts?legal_entity_id='.$entity->id)
        ->assertOk()->assertJsonCount(1, 'data');

    Sanctum::actingAs($user, ['accounting.chart.write']);
    $this->deleteJson("/api/v1/accounting/chart-of-accounts/accounts/{$account->id}")
        ->assertNoContent();
    expect($account->refresh()->is_active)->toBeFalse();
});

it('rejects a parent from another legal entity', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'One', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $otherEntity = LegalEntity::query()->create([
        'name' => 'Two', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    Sanctum::actingAs(User::factory()->create(), ['accounting.chart.write']);

    $parent = Account::query()->create([
        'legal_entity_id' => $otherEntity->id,
        'code' => '1000', 'name' => 'Other Cash', 'type' => 'asset', 'normal_balance' => 'debit',
    ]);

    $this->postJson('/api/v1/accounting/chart-of-accounts/accounts', [
        'legal_entity_id' => $entity->id, 'parent_id' => $parent->id,
        'code' => '1100', 'name' => 'Invalid Child', 'type' => 'asset',
    ])->assertUnprocessable()->assertJsonValidationErrors(['parent_id']);
});

it('rejects duplicate codes and mismatched normal balances', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'Duplicate Chart Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    Sanctum::actingAs(User::factory()->create(), ['accounting.chart.write']);
    Account::query()->create(['legal_entity_id' => $entity->id, 'code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);

    $this->postJson('/api/v1/accounting/chart-of-accounts/accounts', ['legal_entity_id' => $entity->id, 'code' => '1000', 'name' => 'Duplicate', 'type' => 'asset'])->assertUnprocessable()->assertJsonValidationErrors(['account']);
    $this->postJson('/api/v1/accounting/chart-of-accounts/accounts', ['legal_entity_id' => $entity->id, 'code' => '2000', 'name' => 'Invalid Cash', 'type' => 'asset', 'normal_balance' => 'credit'])->assertUnprocessable()->assertJsonValidationErrors(['account']);
});

it('returns a hierarchy tree for a legal entity', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'Tree Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $root = Account::query()->create(['legal_entity_id' => $entity->id, 'code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'normal_balance' => 'debit']);
    Account::query()->create(['legal_entity_id' => $entity->id, 'parent_id' => $root->id, 'code' => '1100', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.chart.read']);

    $this->getJson('/api/v1/accounting/chart-of-accounts/accounts/tree?legal_entity_id='.$entity->id)->assertOk()->assertJsonPath('data.0.children.0.attributes.code', '1100');
});

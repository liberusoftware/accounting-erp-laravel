<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\FinancialMasterDataApi\FinancialMasterDataApiServiceProvider;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(FinancialMasterDataApiServiceProvider::class, force: true);
});

it('creates, updates, lists, and archives a customer through the API', function (): void {
    $user = User::factory()->create();
    $entity = LegalEntity::query()->create(['name' => 'Master Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    Sanctum::actingAs($user, ['accounting.master-data.write']);

    $this->postJson('/api/v1/accounting/financial-master-data/parties', [
        'legal_entity_id' => $entity->id, 'type' => 'customer', 'name' => 'Acme Ltd', 'email' => 'accounts@acme.test',
    ])->assertCreated()->assertJsonPath('data.attributes.type', 'customer');

    $party = Party::query()->firstOrFail();
    $this->patchJson("/api/v1/accounting/financial-master-data/parties/{$party->id}", ['name' => 'Acme Updated'])
        ->assertOk()->assertJsonPath('data.attributes.name', 'Acme Updated');
    Sanctum::actingAs($user, ['accounting.master-data.read']);
    $this->getJson('/api/v1/accounting/financial-master-data/parties?type=customer')->assertOk()->assertJsonCount(1, 'data');
    Sanctum::actingAs($user, ['accounting.master-data.write']);
    $this->deleteJson("/api/v1/accounting/financial-master-data/parties/{$party->id}")->assertNoContent();
    expect($party->refresh()->status->value)->toBe('archived');
});

it('rejects duplicate customer email within a legal entity', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Duplicate Entity', 'currency_code' => 'USD', 'accounting_basis' => 'accrual']);
    Party::query()->create(['legal_entity_id' => $entity->id, 'type' => 'customer', 'name' => 'Existing', 'email' => 'same@example.test']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.write']);

    $this->postJson('/api/v1/accounting/financial-master-data/parties', ['legal_entity_id' => $entity->id, 'type' => 'customer', 'name' => 'Duplicate', 'email' => 'SAME@example.test'])
        ->assertUnprocessable()->assertJsonValidationErrors(['name']);
});

it('exposes item, tax profile, and payment term reference data', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Reference Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.write']);

    $this->postJson('/api/v1/accounting/financial-master-data/payment-terms', ['legal_entity_id' => $entity->id, 'code' => 'NET30', 'name' => 'Net 30', 'days' => 30])->assertCreated();
    $this->postJson('/api/v1/accounting/financial-master-data/tax-profiles', ['legal_entity_id' => $entity->id, 'code' => 'VAT20', 'name' => 'VAT 20%', 'rate' => 20])->assertCreated();
    $this->postJson('/api/v1/accounting/financial-master-data/items-services', ['legal_entity_id' => $entity->id, 'sku' => 'CONSULT', 'name' => 'Consulting', 'kind' => 'service'])->assertCreated();

    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.read']);
    $this->getJson('/api/v1/accounting/financial-master-data/items-services?legal_entity_id='.$entity->id)->assertOk()->assertJsonCount(1, 'data');
});

it('stores party addresses and never returns bank credentials', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Detail Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    $party = Party::query()->create(['legal_entity_id' => $entity->id, 'type' => 'supplier', 'name' => 'Secure Supplier']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.write']);

    $this->postJson("/api/v1/accounting/financial-master-data/parties/{$party->id}/addresses", ['line_one' => '1 Main Street', 'country_code' => 'GB'])
        ->assertCreated();
    $this->postJson("/api/v1/accounting/financial-master-data/parties/{$party->id}/bank-details", ['label' => 'Primary', 'credential_reference' => 'vault://bank/123', 'masked_account' => '****1234'])
        ->assertCreated()->assertJsonMissing(['credential_reference' => 'vault://bank/123']);

    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.read']);
    $this->getJson("/api/v1/accounting/financial-master-data/parties/{$party->id}/bank-details")
        ->assertOk()->assertJsonMissing(['credential_reference']);
});

it('returns a validation response for duplicate reference data', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Duplicate Reference Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.master-data.write']);

    $payload = ['legal_entity_id' => $entity->id, 'code' => 'NET30', 'name' => 'Net 30', 'days' => 30];
    $this->postJson('/api/v1/accounting/financial-master-data/payment-terms', $payload)->assertCreated();
    $this->postJson('/api/v1/accounting/financial-master-data/payment-terms', $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['code']);
});

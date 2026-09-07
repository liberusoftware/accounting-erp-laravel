<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Core\Models\LegalEntity;

uses(RefreshDatabase::class);

it('exposes bank accounts with authorization and masked restricted fields', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Bank API Entity', 'currency_code' => 'USD', 'accounting_basis' => 'accrual']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.bank-accounts.read', 'accounting.bank-accounts.write']);

    $response = $this->postJson('/api/v1/accounting/bank-accounts', [
        'legal_entity_id' => $entity->id, 'name' => 'Operating', 'account_type' => 'current', 'currency' => 'usd',
        'opening_balance' => 500, 'opening_date' => '2026-08-01', 'account_number' => '123456789', 'routing_number' => '987654321',
    ])->assertCreated();

    $response->assertJsonPath('data.attributes.currency', 'USD')
        ->assertJsonPath('data.attributes.masked_account_number', '*****6789')
        ->assertJsonMissingPath('data.attributes.account_number');
    $id = $response->json('data.id');

    $this->getJson("/api/v1/accounting/bank-accounts/{$id}")->assertOk()->assertJsonPath('data.attributes.name', 'Operating');
    $this->getJson('/api/v1/accounting/bank-accounts/balances')->assertOk()->assertJsonPath('data.total', 500);
    $this->postJson("/api/v1/accounting/bank-accounts/{$id}/status", ['status' => 'inactive'])->assertOk()->assertJsonPath('data.attributes.status', 'inactive');
    $this->assertDatabaseHas('accounting_bank_accounts', ['id' => $id, 'status' => 'inactive']);
});

it('rejects bank account reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.bank-accounts.write']);

    $this->getJson('/api/v1/accounting/bank-accounts')->assertForbidden();
});

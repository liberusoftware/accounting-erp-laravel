<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

uses(RefreshDatabase::class);

it('exposes authorized receivable open items and explicit receipt resources', function (): void {
    $party = Party::query()->create(['legal_entity_id' => dbEntity(), 'type' => PartyType::Customer, 'name' => 'API Customer']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.receivables.read', 'accounting.receivables.write']);

    $item = $this->postJson('/api/v1/accounting/accounts-receivable', [
        'party_id' => $party->id, 'reference' => 'AR-API-1', 'issued_on' => '2026-08-20', 'original_amount' => 125, 'currency' => 'USD',
    ])->assertCreated()->assertJsonPath('data.attributes.outstanding', '125.00');

    $this->postJson('/api/v1/accounting/accounts-receivable/receipts', [
        'party_id' => $party->id, 'amount' => 25, 'currency' => 'USD', 'reference' => 'RCPT-API-1',
    ])->assertCreated()->assertJsonPath('data.attributes.unapplied', '25.00');

    $this->getJson('/api/v1/accounting/accounts-receivable')->assertOk()->assertJsonCount(1, 'data');
    expect($item->json('data.type'))->toBe('accounting-ar-open-item');
});

it('rejects receivable access without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.receivables.write']);

    $this->getJson('/api/v1/accounting/accounts-receivable')->assertForbidden();
});

it('keeps receipt and dispute collection routes ahead of the item binding route', function (): void {
    $party = Party::query()->create(['legal_entity_id' => dbEntity(), 'type' => PartyType::Customer, 'name' => 'Collection Customer']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.receivables.read', 'accounting.receivables.write']);

    $this->postJson('/api/v1/accounting/accounts-receivable/receipts', ['party_id' => $party->id, 'amount' => 10, 'currency' => 'USD'])->assertCreated();

    $this->getJson('/api/v1/accounting/accounts-receivable/receipts')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/accounts-receivable/disputes')->assertOk()->assertJsonCount(0, 'data');
});

function dbEntity(): int
{
    return DB::table('accounting_legal_entities')->insertGetId([
        'name' => 'API Receivables Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
    ]);
}

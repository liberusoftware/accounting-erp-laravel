<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

uses(RefreshDatabase::class);

it('exposes authorized payable items and explicit payment resources', function (): void {
    $entityId = DB::table('accounting_legal_entities')->insertGetId(['name' => 'AP API Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()]);
    $supplier = Party::query()->create(['legal_entity_id' => $entityId, 'type' => PartyType::Supplier, 'name' => 'API Supplier']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.payables.read', 'accounting.payables.write']);

    $this->postJson('/api/v1/accounting/accounts-payable/open-items', ['party_id' => $supplier->id, 'reference' => 'AP-API-1', 'issued_on' => '2026-08-20', 'original_amount' => 125, 'currency' => 'USD'])->assertCreated();
    $this->postJson('/api/v1/accounting/accounts-payable/payments', ['party_id' => $supplier->id, 'amount' => 25, 'currency' => 'USD'])->assertCreated()->assertJsonPath('data.type', 'accounting-ap-payment');
    $this->getJson('/api/v1/accounting/accounts-payable')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/accounts-payable/aging')->assertOk();
});

it('rejects payable reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.payables.write']);

    $this->getJson('/api/v1/accounting/accounts-payable')->assertForbidden();
});

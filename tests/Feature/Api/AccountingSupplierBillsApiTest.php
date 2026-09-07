<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;

uses(RefreshDatabase::class);

it('exposes supplier bill lifecycle operations through the scoped API', function (): void {
    $supplier = supplierBillApiParty();
    Sanctum::actingAs(User::factory()->create(), ['accounting.supplier-bills.read', 'accounting.supplier-bills.write']);

    $bill = $this->postJson('/api/v1/accounting/supplier-bills', [
        'party_id' => $supplier->id,
        'bill_number' => 'API-SUP-1',
        'bill_date' => '2026-08-01',
        'due_on' => '2026-08-31',
        'currency' => 'USD',
        'lines' => [['description' => 'Materials', 'quantity' => 2, 'unit_price' => 50, 'tax_rate' => 10]],
    ])->assertCreated()->assertJsonPath('data.attributes.total', 110);

    $id = $bill->json('data.id');
    $this->assertDatabaseHas('accounting_supplier_bills', ['id' => $id]);
    $this->postJson("/api/v1/accounting/supplier-bills/{$id}/approve")
        ->assertOk()->assertJsonPath('data.attributes.status', 'approved');
    $this->getJson('/api/v1/accounting/supplier-bills/duplicates?party_id='.$supplier->id.'&bill_number=API-SUP-1')
        ->assertOk()->assertJsonCount(1, 'data');
});

it('rejects supplier bill access without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.supplier-bills.write']);

    $this->getJson('/api/v1/accounting/supplier-bills')->assertForbidden();
});

function supplierBillApiParty(): Party
{
    $entityId = DB::table('accounting_legal_entities')->insertGetId([
        'name' => 'Supplier Bills API Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
    ]);

    return Party::query()->create(['legal_entity_id' => $entityId, 'type' => PartyType::Supplier, 'name' => 'API Supplier']);
}

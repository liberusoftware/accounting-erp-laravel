<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SupplierBills\Actions\CreateSupplierBill;
use Liberu\Accounting\SupplierBillsFilament\Resources\SupplierBillResource;
use Liberu\Accounting\SupplierBillsFilament\SupplierBillsFilamentPlugin;
use Liberu\Accounting\SupplierBillsLivewire\Livewire\SupplierBills;
use Liberu\Accounting\SupplierBillsLivewire\SupplierBillsLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(SupplierBillsLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes the supplier bills Filament plugin and Livewire component boundary', function (): void {
    expect(SupplierBillsFilamentPlugin::make()->getId())->toBe('accounting-supplier-bills')
        ->and(SupplierBillResource::getModel())->toBe('Liberu\\Accounting\\SupplierBills\\Models\\SupplierBill')
        ->and(SupplierBills::class)->toBeString();
});

it('renders supplier bills through the namespaced compatibility alias', function (): void {
    $supplier = supplierBillPresentationParty();
    app(CreateSupplierBill::class)->handle([
        'party_id' => $supplier->id, 'bill_number' => 'PRESENTATION-SUP-1', 'bill_date' => '2026-08-01', 'currency' => 'USD',
    ], [['description' => 'Service', 'quantity' => 1, 'unit_price' => 25]]);

    Livewire::test('module-accounting-supplier-bills-supplier-bills')
        ->assertOk()
        ->assertSee('PRESENTATION-SUP-1');
});

function supplierBillPresentationParty(): Party
{
    $entityId = DB::table('accounting_legal_entities')->insertGetId([
        'name' => 'Supplier Bills Presentation Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
    ]);

    return Party::query()->create(['legal_entity_id' => $entityId, 'type' => PartyType::Supplier, 'name' => 'Presentation Supplier']);
}

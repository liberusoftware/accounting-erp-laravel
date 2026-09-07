<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayableLivewire\AccountsPayableLivewireServiceProvider;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(AccountsPayableLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('registers and renders the payable presentation components', function (): void {
    $supplier = payablePresentationSupplier();

    Livewire::test('module-accounting-accounts-payable-aging', ['partyId' => $supplier->id])
        ->assertSee('Payables aging')
        ->assertSee('current');
    Livewire::test('module-accounting-accounts-payable-reconciliation')->assertSee('Accounts Payable reconciliation');
});

it('renders the supplier subledger compatibility alias', function (): void {
    $supplier = payablePresentationSupplier();

    Livewire::test('module-accounting-accounts-payable-payables', ['partyId' => $supplier->id])
        ->assertOk()
        ->assertSee('Payables');
});

function payablePresentationSupplier(): Party
{
    $entityId = DB::table('accounting_legal_entities')->insertGetId([
        'name' => 'AP Presentation Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
    ]);

    return Party::query()->create(['legal_entity_id' => $entityId, 'type' => PartyType::Supplier, 'name' => 'Presentation Supplier']);
}

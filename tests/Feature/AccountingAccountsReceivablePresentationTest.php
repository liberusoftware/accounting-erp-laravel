<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivableFilament\AccountsReceivableFilamentPlugin;
use Liberu\Accounting\AccountsReceivableFilament\Resources\ReceivableOpenItemResource;
use Liberu\Accounting\AccountsReceivableLivewire\AccountsReceivableLivewireServiceProvider;
use Liberu\Accounting\FinancialMasterData\Enums\PartyType;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(AccountsReceivableLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes bounded Filament and Livewire AR presentation identities', function (): void {
    expect(AccountsReceivableFilamentPlugin::make()->getId())->toBe('accounting-accounts-receivable')
        ->and(ReceivableOpenItemResource::getModel())->toBe(ReceivableOpenItem::class);
});

it('renders customer subledger, aging, and statement components', function (): void {
    $party = receivablePresentationParty();
    app(CreateOpenItem::class)->handle([
        'party_id' => $party->id,
        'reference' => 'AR-PRESENTATION-1',
        'issued_on' => '2026-08-01',
        'original_amount' => 100,
        'currency' => 'USD',
    ]);

    Livewire::test('module-accounting-accounts-receivable-receivables', ['partyId' => $party->id])
        ->assertOk()
        ->assertSee('AR-PRESENTATION-1');
    Livewire::test('module-accounting-accounts-receivable-aging', ['partyId' => $party->id])
        ->assertOk()
        ->assertSee('current');
    Livewire::test('module-accounting-accounts-receivable-statement', ['partyId' => $party->id])
        ->assertOk()
        ->assertSee('Closing balance');
});

function receivablePresentationParty(): Party
{
    $entityId = DB::table('accounting_legal_entities')->insertGetId([
        'name' => 'AR Presentation Entity', 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now(),
    ]);

    return Party::query()->create(['legal_entity_id' => $entityId, 'type' => PartyType::Customer, 'name' => 'Presentation Customer']);
}

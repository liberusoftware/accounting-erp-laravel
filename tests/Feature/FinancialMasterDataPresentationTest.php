<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\FinancialMasterDataFilament\FinancialMasterDataFilamentPlugin;
use Liberu\Accounting\FinancialMasterDataFilament\Resources\PartyResource;
use Liberu\Accounting\FinancialMasterDataLivewire\FinancialMasterDataLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(FinancialMasterDataLivewireServiceProvider::class, force: true);
});

it('exposes matching Filament and Livewire boundaries', function (): void {
    expect(FinancialMasterDataFilamentPlugin::make()->getId())->toBe('liberu-accounting-financial-master-data')
        ->and(PartyResource::getModel())->toBe('Liberu\\Accounting\\FinancialMasterData\\Models\\Party');
});

it('creates a supplier through Livewire', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Livewire Master', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    Livewire::test('module-accounting-financial-master-data-parties')
        ->set('legalEntityId', (string) $entity->id)->set('type', 'supplier')->set('name', 'Supplier One')->set('email', 'supplier@example.test')
        ->call('save')->assertDispatched('party-created');
    $this->assertDatabaseHas('accounting_master_parties', ['legal_entity_id' => $entity->id, 'type' => 'supplier', 'name' => 'Supplier One']);
});

it('creates tax reference data through Livewire', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Reference Livewire', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    Livewire::test('module-accounting-financial-master-data-reference-data')
        ->set('resource', 'tax-profiles')->set('legalEntityId', (string) $entity->id)->set('code', 'VAT20')->set('name', 'VAT 20%')->set('rate', '20')
        ->call('save')->assertDispatched('reference-data-created');
    $this->assertDatabaseHas('accounting_master_tax_profiles', ['legal_entity_id' => $entity->id, 'code' => 'VAT20', 'rate' => 20]);
});

it('registers the compatibility Livewire aliases', function (): void {
    Livewire::test('financial-master-data-parties')->assertStatus(200);
    Livewire::test('financial-master-data-reference-data')->assertStatus(200);
});

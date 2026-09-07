<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ChartOfAccountsFilament\ChartOfAccountsFilamentPlugin;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource;
use Liberu\Accounting\ChartOfAccountsLivewire\ChartOfAccountsLivewireServiceProvider;
use Liberu\Accounting\Core\Models\LegalEntity;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(ChartOfAccountsLivewireServiceProvider::class, force: true);
});

it('exposes one-to-one Filament and Livewire boundaries', function (): void {
    expect(ChartOfAccountsFilamentPlugin::make()->getId())->toBe('liberu-accounting-chart-of-accounts')
        ->and(AccountResource::getModel())->toBe('Liberu\\Accounting\\ChartOfAccounts\\Models\\Account');
});

it('creates accounts through the Livewire boundary', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'Livewire Entity', 'currency_code' => 'USD', 'accounting_basis' => 'accrual',
    ]);

    Livewire::test('module-accounting-chart-of-accounts-accounts')
        ->set('legalEntityId', (string) $entity->id)
        ->set('code', '4000')
        ->set('name', 'Revenue')
        ->set('type', 'revenue')
        ->call('save')
        ->assertDispatched('account-created');

    $this->assertDatabaseHas('accounting_chart_accounts', [
        'legal_entity_id' => $entity->id, 'code' => '4000', 'normal_balance' => 'credit',
    ]);
});

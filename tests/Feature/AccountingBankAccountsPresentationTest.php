<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\BankAccountsFilament\BankAccountsFilamentPlugin;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource;
use Liberu\Accounting\BankAccountsLivewire\BankAccountsLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(BankAccountsLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes bounded bank account Filament and Livewire identities', function (): void {
    expect(BankAccountsFilamentPlugin::make()->getId())->toBe('accounting-bank-accounts')
        ->and(BankAccountResource::getModel())->toBe('Liberu\\Accounting\\BankAccounts\\Models\\BankAccount');

    Livewire::test('module-accounting-bank-accounts-accounts')
        ->assertOk()
        ->assertSee('Bank accounts')
        ->assertSee('No bank accounts.');
});

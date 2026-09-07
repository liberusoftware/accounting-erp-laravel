<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\BankRulesFilament\BankRulesFilamentPlugin;
use Liberu\Accounting\BankRulesFilament\Resources\BankRuleResource;
use Liberu\Accounting\BankRulesLivewire\BankRulesLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(BankRulesLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes bounded Bank Rules Filament and Livewire identities', function (): void {
    expect(BankRulesFilamentPlugin::make()->getId())->toBe('accounting-bank-rules')
        ->and(BankRuleResource::getModel())->toBe('Liberu\\Accounting\\BankRules\\Models\\BankRule');

    Livewire::test('module-accounting-bank-rules-rules')
        ->assertOk()
        ->assertSee('Bank rules')
        ->assertSee('No bank rules.');
});

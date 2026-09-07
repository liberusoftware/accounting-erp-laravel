<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\BillPaymentsFilament\BillPaymentsFilamentPlugin;
use Liberu\Accounting\BillPaymentsFilament\Resources\BillPaymentResource;
use Liberu\Accounting\BillPaymentsLivewire\BillPaymentsLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(BillPaymentsLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes bounded bill payment Filament and Livewire identities', function (): void {
    expect(BillPaymentsFilamentPlugin::make()->getId())->toBe('accounting-bill-payments')
        ->and(BillPaymentResource::getModel())->toBe('Liberu\\Accounting\\BillPayments\\Models\\BillPaymentProposal');

    Livewire::test('module-accounting-bill-payments-proposals')
        ->assertOk()
        ->assertSee('Bill payments')
        ->assertSee('No bill payment proposals.');
});

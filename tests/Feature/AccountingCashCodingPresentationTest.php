<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\CashCodingFilament\CashCodingFilamentPlugin;
use Liberu\Accounting\CashCodingFilament\Resources\CashCodingBatchResource;
use Liberu\Accounting\CashCodingLivewire\CashCodingLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $provider = $this->app->register(CashCodingLivewireServiceProvider::class, force: true);
    $provider->boot();
    $this->actingAs(User::factory()->create());
});

it('exposes cash coding presentation boundaries', function (): void {
    expect(CashCodingFilamentPlugin::make()->getId())->toBe('accounting-cash-coding')->and(CashCodingBatchResource::getModel())->toBe('Liberu\\Accounting\\CashCoding\\Models\\CashCodingBatch');
    Livewire::test('module-accounting-cash-coding-batches')->assertOk()->assertSee('Cash coding')->assertSee('No cash coding batches.');
});

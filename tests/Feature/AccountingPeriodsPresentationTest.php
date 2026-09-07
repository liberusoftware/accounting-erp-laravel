<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\PeriodsFilament\AccountingPeriodsFilamentPlugin;
use Liberu\Accounting\PeriodsFilament\Resources\AccountingPeriodResource;
use Liberu\Accounting\PeriodsLivewire\AccountingPeriodsLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(AccountingPeriodsLivewireServiceProvider::class, force: true);
});

it('exposes the Accounting Periods Filament and Livewire boundaries', function (): void {
    expect(AccountingPeriodsFilamentPlugin::make()->getId())->toBe('liberu-accounting-periods')
        ->and(AccountingPeriodResource::getModel())->toBe('Liberu\\Accounting\\Periods\\Models\\AccountingPeriod');
});

it('creates a period through Livewire', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'Periods Livewire', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $book = Book::query()->create([
        'legal_entity_id' => $entity->id, 'name' => 'Livewire Book', 'code' => 'LWB',
        'accounting_basis' => 'accrual', 'is_active' => true,
    ]);

    Livewire::test('module-accounting-periods-periods')
        ->set('bookId', (string) $book->id)
        ->set('startsOn', '2026-02-01')
        ->set('endsOn', '2026-02-28')
        ->call('save')
        ->assertDispatched('period-created');

    $this->assertDatabaseHas('accounting_periods', ['book_id' => $book->id, 'state' => 'open']);
});

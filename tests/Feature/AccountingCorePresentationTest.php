<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\CoreFilament\AccountingCoreFilamentPlugin;
use Liberu\Accounting\CoreFilament\Resources\BookResource;
use Liberu\Accounting\CoreLivewire\AccountingCoreLivewireServiceProvider;
use Liberu\Accounting\CoreLivewire\Livewire\AccountingSettings;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->app->register(AccountingCoreLivewireServiceProvider::class, force: true);
});

it('exposes book and settings presentation boundaries', function (): void {
    expect(AccountingCoreFilamentPlugin::make()->getId())->toBe('liberu-accounting-core')
        ->and(BookResource::getModel())->toBe('Liberu\\Accounting\\Core\\Models\\Book')
        ->and(AccountingSettings::class)->toBeString();
});

it('creates accounting defaults through Livewire', function (): void {
    $entity = LegalEntity::query()->create([
        'name' => 'Settings Livewire', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $book = Book::query()->create([
        'legal_entity_id' => $entity->id, 'name' => 'Main Book', 'code' => 'MAIN',
        'accounting_basis' => 'accrual', 'is_active' => true,
    ]);

    Livewire::test('module-accounting-core-settings')
        ->set('bookId', (string) $book->id)
        ->set('key', 'tax_profile')
        ->set('value', '{"code":"STANDARD"}')
        ->call('save')
        ->assertDispatched('accounting-setting-saved');

    $this->assertDatabaseHas('accounting_defaults', [
        'book_id' => $book->id, 'key' => 'tax_profile',
    ]);
});

<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\PoliciesFilament\AccountingPoliciesFilamentPlugin;
use Liberu\Accounting\PoliciesFilament\Resources\PolicyRuleResource;
use Liberu\Accounting\PoliciesLivewire\AccountingPoliciesLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);
beforeEach(function (): void {
    $this->app->register(AccountingPoliciesLivewireServiceProvider::class, force: true);
});
it('exposes policy presentation boundaries', function (): void {
    expect(AccountingPoliciesFilamentPlugin::make()->getId())->toBe('liberu-accounting-policies')->and(PolicyRuleResource::getModel())->toBe('Liberu\\Accounting\\Policies\\Models\\PolicyRule');
});
it('creates a policy rule through Livewire', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'Policy UI', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    $book = Book::query()->create(['legal_entity_id' => $entity->id, 'name' => 'Policy UI Book', 'code' => 'PUI', 'accounting_basis' => 'accrual', 'is_active' => true]);
    Livewire::test('module-accounting-policies-policy-rules')->set('bookId', (string) $book->id)->set('key', 'rounding_mode')->set('value', '{"mode":"half_up"}')->set('effectiveFrom', '2026-01-01')->call('save')->assertHasNoErrors();
    $this->assertDatabaseHas('accounting_policy_rules', ['book_id' => $book->id, 'key' => 'rounding_mode']);
});

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;

uses(RefreshDatabase::class);

it('requires authentication for accounting core legal entities', function (): void {
    $this->getJson('/api/v1/accounting/accounting-core/legal-entities')
        ->assertUnauthorized();
});

it('requires the accounting core read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['invoices:read']);

    $this->getJson('/api/v1/accounting/accounting-core/legal-entities')
        ->assertForbidden();
});

it('creates and lists legal entities through the scoped contract', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['accounting.core.write']);

    $this->postJson('/api/v1/accounting/accounting-core/legal-entities', [
        'name' => 'Liberu Limited',
        'registration_number' => 'GB-123',
        'currency_code' => 'GBP',
        'accounting_basis' => 'accrual',
    ])->assertCreated()
        ->assertJsonPath('data.type', 'accounting-legal-entity')
        ->assertJsonPath('data.attributes.currency_code', 'GBP');

    Sanctum::actingAs($user, ['accounting.core.read']);

    $this->getJson('/api/v1/accounting/accounting-core/legal-entities')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.attributes.name', 'Liberu Limited');
});

it('rejects invalid currency input at the API boundary', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.core.write']);

    $this->postJson('/api/v1/accounting/accounting-core/legal-entities', [
        'name' => 'Invalid Limited',
        'currency_code' => 'gbp',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['currency_code']);
});

it('updates and deletes legal entities with the write ability', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.core.write']);

    $this->postJson('/api/v1/accounting/accounting-core/legal-entities', [
        'name' => 'Original Entity',
        'currency_code' => 'GBP',
        'accounting_basis' => 'accrual',
    ])->assertCreated();

    $entity = LegalEntity::query()->firstOrFail();

    $this->patchJson("/api/v1/accounting/accounting-core/legal-entities/{$entity->id}", [
        'name' => 'Updated Entity',
        'is_active' => false,
    ])->assertOk()
        ->assertJsonPath('data.attributes.name', 'Updated Entity')
        ->assertJsonPath('data.attributes.is_active', false);

    $this->deleteJson("/api/v1/accounting/accounting-core/legal-entities/{$entity->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('accounting_legal_entities', ['id' => $entity->id]);
});

it('manages books and rejects overlapping fiscal calendars', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.core.write', 'accounting.core.read']);

    $entity = LegalEntity::query()->create([
        'name' => 'Books Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual', 'is_active' => true,
    ]);

    $book = $this->postJson('/api/v1/accounting/accounting-core/books', [
        'legal_entity_id' => $entity->id, 'name' => '2026 Book', 'code' => '2026', 'accounting_basis' => 'accrual',
    ])->assertCreated()->assertJsonPath('data.type', 'accounting-book')->json('data');

    $bookId = $book['id'];
    $this->postJson("/api/v1/accounting/accounting-core/books/{$bookId}/fiscal-calendars", [
        'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31',
    ])->assertCreated();

    $this->postJson("/api/v1/accounting/accounting-core/books/{$bookId}/fiscal-calendars", [
        'starts_on' => '2026-06-01', 'ends_on' => '2027-05-31',
    ])->assertUnprocessable();

    $this->postJson("/api/v1/accounting/accounting-core/books/{$bookId}/numbering-sequences", [
        'key' => 'invoice', 'prefix' => 'INV-', 'next_number' => 1, 'padding' => 6,
    ])->assertCreated()->assertJsonPath('data.attributes.padding', 6)
        ->tap(function ($response) use ($bookId): void {
            $sequenceId = $response->json('data.id');
            test()->postJson("/api/v1/accounting/accounting-core/books/{$bookId}/numbering-sequences/{$sequenceId}/allocate")
                ->assertOk()->assertJsonPath('data.number', 'INV-000001');
        });
});

it('manages book defaults and policies within the book boundary', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.core.write', 'accounting.core.read']);
    $entity = LegalEntity::query()->create([
        'name' => 'Settings Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual', 'is_active' => true,
    ]);
    $book = Book::query()->create([
        'legal_entity_id' => $entity->id, 'name' => 'Settings Book', 'code' => 'SET', 'accounting_basis' => 'accrual', 'is_active' => true,
    ]);

    $default = $this->postJson("/api/v1/accounting/accounting-core/books/{$book->id}/defaults", [
        'key' => 'sales_tax_profile', 'value' => ['code' => 'STANDARD'],
    ])->assertCreated()->assertJsonPath('data.type', 'accounting-default')->json('data');
    $this->postJson("/api/v1/accounting/accounting-core/books/{$book->id}/policies", [
        'key' => 'period_locking', 'value' => ['enabled' => true],
    ])->assertCreated()->assertJsonPath('data.attributes.value.enabled', true);

    $this->getJson("/api/v1/accounting/accounting-core/books/{$book->id}/defaults")
        ->assertOk()->assertJsonPath('data.0.attributes.key', 'sales_tax_profile');
    $this->patchJson("/api/v1/accounting/accounting-core/books/{$book->id}/defaults/{$default['id']}", [
        'value' => ['code' => 'REDUCED'],
    ])->assertOk()->assertJsonPath('data.attributes.value.code', 'REDUCED');
});

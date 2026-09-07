<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\Policies\Models\PolicyRule;

uses(RefreshDatabase::class);
it('creates and resolves effective-dated policy rules', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.policies.read', 'accounting.policies.write']);
    $entity = LegalEntity::query()->create(['name' => 'Policy Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    $book = Book::query()->create(['legal_entity_id' => $entity->id, 'name' => 'Policy Book', 'code' => 'POL', 'accounting_basis' => 'accrual', 'is_active' => true]);
    $rule = $this->postJson('/api/v1/accounting/accounting-policies/policies', ['book_id' => $book->id, 'category' => 'rounding', 'key' => 'tax', 'value' => ['mode' => 'half_up'], 'effective_from' => '2026-01-01'])->assertCreated()->assertJsonPath('data.attributes.category', 'rounding')->json('data');
    $this->getJson("/api/v1/accounting/accounting-policies/resolve?book_id={$book->id}&category=rounding&key=tax&date=2026-06-01")->assertOk()->assertJsonPath('data.id', $rule['id']);
    $this->getJson('/api/v1/accounting/accounting-policies/policies')->assertOk()->assertJsonCount(1, 'data');
});
it('rejects overlapping effective dates', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.policies.write']);
    $entity = LegalEntity::query()->create(['name' => 'Policy Overlap', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual']);
    $book = Book::query()->create(['legal_entity_id' => $entity->id, 'name' => 'Policy Book', 'code' => 'POV', 'accounting_basis' => 'accrual', 'is_active' => true]);
    PolicyRule::query()->create(['book_id' => $book->id, 'category' => 'tax', 'key' => 'default', 'value' => ['code' => 'A'], 'effective_from' => '2026-01-01', 'effective_until' => '2026-12-31', 'is_active' => true]);
    $this->postJson('/api/v1/accounting/accounting-policies/policies', ['book_id' => $book->id, 'category' => 'tax', 'key' => 'default', 'value' => ['code' => 'B'], 'effective_from' => '2026-06-01', 'effective_until' => '2027-01-01'])->assertUnprocessable();
});

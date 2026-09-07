<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\Periods\Models\AccountingPeriod;

uses(RefreshDatabase::class);

it('requires the accounting periods read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.core.read']);
    $this->getJson('/api/v1/accounting/accounting-periods/periods')->assertForbidden();
});

it('creates, transitions, locks, and checks posting dates', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.periods.read', 'accounting.periods.write']);
    $entity = LegalEntity::query()->create([
        'name' => 'Periods Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $book = Book::query()->create([
        'legal_entity_id' => $entity->id, 'name' => 'Periods Book', 'code' => 'PER',
        'accounting_basis' => 'accrual', 'is_active' => true,
    ]);

    $period = $this->postJson('/api/v1/accounting/accounting-periods/periods', [
        'book_id' => $book->id, 'starts_on' => '2026-01-01', 'ends_on' => '2026-01-31',
    ])->assertCreated()->assertJsonPath('data.attributes.state', 'open')->json('data');

    $periodId = $period['id'];
    $this->getJson("/api/v1/accounting/accounting-periods/periods/{$periodId}/posting-allowed?date=2026-01-15")
        ->assertOk()->assertJsonPath('data.allowed', true);
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$periodId}/transition", ['state' => 'soft_closed'])
        ->assertOk()->assertJsonPath('data.attributes.state', 'soft_closed');
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$periodId}/transition", ['state' => 'hard_closed'])
        ->assertOk()->assertJsonPath('data.attributes.state', 'hard_closed');
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$periodId}/lock")
        ->assertOk()->assertJsonPath('data.attributes.locked_by', (string) $user->id);
    $this->getJson("/api/v1/accounting/accounting-periods/periods/{$periodId}/posting-allowed?date=2026-01-15")
        ->assertOk()->assertJsonPath('data.allowed', false);
});

it('rejects overlapping periods and requires a reason to reopen', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.periods.write']);
    $entity = LegalEntity::query()->create([
        'name' => 'Overlap Entity', 'currency_code' => 'GBP', 'accounting_basis' => 'accrual',
    ]);
    $book = Book::query()->create([
        'legal_entity_id' => $entity->id, 'name' => 'Overlap Book', 'code' => 'OVR',
        'accounting_basis' => 'accrual', 'is_active' => true,
    ]);
    $period = AccountingPeriod::query()->create(['book_id' => $book->id, 'starts_on' => '2026-01-01', 'ends_on' => '2026-01-31', 'state' => 'open']);
    $this->postJson('/api/v1/accounting/accounting-periods/periods', ['book_id' => $book->id, 'starts_on' => '2026-01-15', 'ends_on' => '2026-02-15'])->assertUnprocessable();
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$period->id}/transition", ['state' => 'soft_closed'])->assertOk();
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$period->id}/transition", ['state' => 'open'])->assertUnprocessable();
    $this->postJson("/api/v1/accounting/accounting-periods/periods/{$period->id}/transition", ['state' => 'open', 'reason' => 'Correction approved'])->assertOk();
});

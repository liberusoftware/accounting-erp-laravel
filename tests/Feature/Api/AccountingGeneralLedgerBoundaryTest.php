<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\LegalEntity;

uses(RefreshDatabase::class);

it('creates and posts a balanced journal through the authorized API boundary', function (): void {
    $entity = LegalEntity::query()->create(['name' => 'GL API Entity', 'currency_code' => 'USD', 'accounting_basis' => 'accrual']);
    $book = Book::query()->create(['legal_entity_id' => $entity->id, 'name' => 'GL API Book', 'code' => 'GLAPI', 'accounting_basis' => 'accrual']);
    $cash = Account::query()->create(['legal_entity_id' => $entity->id, 'code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);
    $revenue = Account::query()->create(['legal_entity_id' => $entity->id, 'code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'normal_balance' => 'credit']);
    Sanctum::actingAs(User::factory()->create(), ['accounting.general-ledger.read', 'accounting.general-ledger.write']);

    $response = $this->postJson('/api/v1/accounting/general-ledger', ['book_id' => $book->id, 'entry_date' => '2026-08-25', 'lines' => [['account_id' => $cash->id, 'debit' => 100], ['account_id' => $revenue->id, 'credit' => 100]]])->assertCreated();
    $journalId = $response->json('data.id');

    $this->postJson("/api/v1/accounting/general-ledger/{$journalId}/post")->assertOk()->assertJsonPath('data.attributes.status', 'posted');
    $this->getJson('/api/v1/accounting/general-ledger/balances?book_id='.$book->id)->assertOk();
});

it('rejects general-ledger reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.general-ledger.write']);

    $this->getJson('/api/v1/accounting/general-ledger')->assertForbidden();
});

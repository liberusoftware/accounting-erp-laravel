<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports the authorized reconciliation lifecycle and summary', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.bank-reconciliation.read', 'accounting.bank-reconciliation.write']);

    $response = $this->postJson('/api/v1/accounting/bank-reconciliation', [
        'bank_account_id' => 77, 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'opening_balance' => 100, 'statement_balance' => 150,
    ])->assertCreated();
    $id = $response->json('data.id');

    $entry = $this->postJson("/api/v1/accounting/bank-reconciliation/{$id}/entries", [
        'source_type' => 'bank-feed-transaction', 'source_id' => 'txn-1', 'kind' => 'match', 'amount' => 50, 'currency' => 'USD',
    ])->assertCreated();
    $entryId = $entry->json('id');
    $this->postJson("/api/v1/accounting/bank-reconciliation/{$id}/entries/{$entryId}/confirm")->assertOk();
    $this->getJson("/api/v1/accounting/bank-reconciliation/{$id}/summary")->assertOk()->assertJsonPath('data.variance', 0);
    $this->postJson("/api/v1/accounting/bank-reconciliation/{$id}/sign-off")->assertOk()->assertJsonPath('data.attributes.status', 'signed_off');
});

it('denies reconciliation reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.bank-reconciliation.write']);
    $this->getJson('/api/v1/accounting/bank-reconciliation')->assertForbidden();
});

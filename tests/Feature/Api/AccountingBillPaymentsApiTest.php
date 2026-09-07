<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports the authorized bill payment proposal lifecycle', function (): void {
    $maker = User::factory()->create();
    $checker = User::factory()->create();

    Sanctum::actingAs($maker, ['accounting.bill-payments.read', 'accounting.bill-payments.write']);

    $response = $this->postJson('/api/v1/accounting/bill-payments', [
        'supplier_id' => 42,
        'bill_reference' => 'INV-2026-001',
        'amount' => 1000,
        'currency' => 'usd',
        'due_date' => '2026-09-30',
        'discount_date' => '2026-09-10',
        'discount_rate' => 2.5,
        'bank_details' => ['beneficiary_name' => 'Supplier Ltd', 'iban' => 'DE89370400440532013000'],
        'provider' => 'wise',
    ])->assertCreated();

    $id = $response->json('data.id');
    expect($response->json('data.attributes.currency'))->toBe('USD');

    $this->postJson("/api/v1/accounting/bill-payments/{$id}/request-approval")
        ->assertOk()
        ->assertJsonPath('data.attributes.status', 'pending_approval');

    Sanctum::actingAs($checker, ['accounting.bill-payments.read', 'accounting.bill-payments.write']);

    $this->postJson("/api/v1/accounting/bill-payments/{$id}/approve")
        ->assertOk()
        ->assertJsonPath('data.attributes.status', 'approved');

    $this->getJson("/api/v1/accounting/bill-payments/{$id}/optimization")
        ->assertOk()
        ->assertJsonPath('data.discount_amount', 25);
});

it('denies bill payment reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.bill-payments.write']);

    $this->getJson('/api/v1/accounting/bill-payments')->assertForbidden();
});

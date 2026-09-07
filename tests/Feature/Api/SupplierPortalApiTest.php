<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('requires supplier portal read access', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, []);
    $this->getJson('/api/v1/accounting/supplier-portal')->assertForbidden();

    Sanctum::actingAs($user, ['accounting.supplier-portal.read']);
    $this->getJson('/api/v1/accounting/supplier-portal')->assertOk()->assertJsonCount(0, 'data');
});

it('requires supplier portal write access and returns created resources', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.supplier-portal.read']);
    $payload = ['supplier_id' => 'supplier-1', 'type' => 'invoice', 'reference' => 'INV-1', 'currency' => 'GBP', 'amount' => 100];
    $this->postJson('/api/v1/accounting/supplier-portal', $payload)->assertForbidden();

    Sanctum::actingAs($user, ['accounting.supplier-portal.write']);
    $this->postJson('/api/v1/accounting/supplier-portal', $payload)->assertCreated();
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\TaxCore\Models\TaxRule;

uses(RefreshDatabase::class);

it('requires the tax core read ability', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, []);
    $this->getJson('/api/v1/accounting/tax-core')->assertForbidden();

    Sanctum::actingAs($user, ['accounting.tax-core.read']);
    TaxRule::query()->create(['code' => 'VAT20', 'name' => 'VAT', 'tax_type' => 'vat', 'rate' => 20, 'treatment' => 'exclusive', 'effective_from' => '2026-01-01', 'status' => 'draft', 'rounding_method' => 'half_up', 'rounding_scale' => 2]);
    $this->getJson('/api/v1/accounting/tax-core')->assertOk()->assertJsonCount(1, 'data');
});

it('requires tax core write access and returns created rules', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['accounting.tax-core.read']);
    $payload = ['code' => 'VAT5', 'name' => 'Reduced VAT', 'tax_type' => 'vat', 'rate' => 5, 'effective_from' => '2026-01-01'];
    $this->postJson('/api/v1/accounting/tax-core', $payload)->assertForbidden();

    Sanctum::actingAs($user, ['accounting.tax-core.write']);
    $this->postJson('/api/v1/accounting/tax-core', $payload)->assertCreated();
});

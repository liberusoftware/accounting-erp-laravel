<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates dimensions, values, validates them, and allocates through the API', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.dimensions.read', 'accounting.dimensions.write']);

    $dimension = $this->postJson('/api/v1/accounting/dimensions-and-tracking', ['code' => 'department', 'name' => 'Department', 'kind' => 'department', 'is_required' => true])->assertCreated();
    $dimensionId = $dimension->json('data.id');
    $this->postJson("/api/v1/accounting/dimensions-and-tracking/{$dimensionId}/values", ['code' => 'sales', 'name' => 'Sales'])->assertCreated();
    $this->postJson('/api/v1/accounting/dimensions-and-tracking/validate', ['dimensions' => ['department' => ['sales']]])->assertOk()->assertJsonPath('data.valid', true);
    $this->postJson('/api/v1/accounting/dimensions-and-tracking/allocate', ['allocation_key' => 'AL-1', 'amount' => 100, 'allocations' => [['percentage' => 100, 'dimensions' => ['department' => ['sales']]]]])->assertCreated();
    $this->getJson('/api/v1/accounting/dimensions-and-tracking/balances?allocation_key=AL-1')->assertOk();
});

it('rejects dimension reads without the read ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.dimensions.write']);

    $this->getJson('/api/v1/accounting/dimensions-and-tracking')->assertForbidden();
});

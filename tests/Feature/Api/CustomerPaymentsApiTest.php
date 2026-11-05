<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);

it('records and allocates customer payments through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Customer Payments API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.customer-payments.read', 'accounting.customer-payments.write']);
    $payment = $this->postJson('/api/v1/accounting/customer-payments', ['customer_id' => 'cust-api', 'kind' => 'receipt', 'reference' => 'API-REC', 'currency' => 'GBP', 'amount' => 120])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/customer-payments/{$payment['id']}/allocations", ['document_ref' => 'INV-API', 'amount' => 120])->assertCreated();
    $this->getJson('/api/v1/accounting/customer-payments')->assertOk()->assertJsonCount(1, 'data');
});

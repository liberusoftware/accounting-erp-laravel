<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);

it('creates customer portal records through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Customer Portal API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.customer-portal.read', 'accounting.customer-portal.write']);
    $this->postJson('/api/v1/accounting/customer-portal', ['customer_id' => 'cust-api', 'type' => 'invoice', 'reference' => 'API-INV', 'amount' => 80])->assertCreated();
    $this->getJson('/api/v1/accounting/customer-portal')->assertOk()->assertJsonCount(1, 'data');
});

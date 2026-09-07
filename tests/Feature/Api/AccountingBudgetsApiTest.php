<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports adding lines and approving a budget through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Budget Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.budgets.read', 'accounting.budgets.write']);

    $response = $this->postJson('/api/v1/accounting/budgets', [
        'name' => 'FY2027 plan',
        'period_start' => '2027-01-01',
        'period_end' => '2027-12-31',
        'currency' => 'GBP',
    ])->assertCreated();
    $id = $response->json('data.id');

    $this->postJson("/api/v1/accounting/budgets/{$id}/lines", [
        'account_id' => 200,
        'planned_amount' => 12000,
        'phases' => ['2027-01' => 1000],
    ])->assertOk();
    $this->postJson("/api/v1/accounting/budgets/{$id}/submit")->assertOk();
    $this->postJson("/api/v1/accounting/budgets/{$id}/approve")->assertOk()->assertJsonPath('data.attributes.status', 'approved');
});

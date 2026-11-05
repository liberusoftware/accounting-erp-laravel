<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);
it('creates and submits an audit request for the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Audit Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.audit-support.read', 'accounting.audit-support.write']);
    $response = $this->postJson('/api/v1/accounting/audit-support', ['title' => 'Provide bank statements', 'evidence' => [['name' => 'January statement']]])->assertCreated();
    $id = $response->json('data.id');
    $this->postJson("/api/v1/accounting/audit-support/{$id}/submit")->assertOk()->assertJsonPath('data.attributes.status', 'submitted');
});

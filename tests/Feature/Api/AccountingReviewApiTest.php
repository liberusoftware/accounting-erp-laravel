<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates, resolves, and signs off a review item within the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Review Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.review.read', 'accounting.review.write']);

    $response = $this->postJson('/api/v1/accounting/review', [
        'item_type' => 'uncategorized_transaction',
        'severity' => 'high',
        'title' => 'Categorize bank transaction',
    ])->assertCreated();
    $id = $response->json('data.id');

    $this->postJson("/api/v1/accounting/review/{$id}/resolve", [
        'summary' => 'Assigned to office supplies.',
    ])->assertOk()->assertJsonPath('data.attributes.status', 'resolved');
    $this->postJson("/api/v1/accounting/review/{$id}/sign-off", [
        'attestation' => 'Reviewed and approved.',
    ])->assertOk()->assertJsonPath('data.attributes.status', 'signed_off');
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Workpapers\Models\Workpaper;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('manages workpapers and procedures through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.workpapers.read', 'accounting.workpapers.write']);

    $this->postJson('/api/v1/accounting/workpapers', ['title' => 'Year-end bank workpaper'])->assertCreated();
    $workpaper = Workpaper::query()->firstOrFail();
    $this->getJson('/api/v1/accounting/workpapers')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/references', ['label' => 'Trial balance'])->assertCreated();
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/attachments', ['name' => 'trial-balance.csv', 'path' => 'workpapers/trial-balance.csv'])->assertCreated();
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/procedures', ['description' => 'Inspect supporting evidence'])->assertCreated();
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/reviewer', ['reviewer_id' => 9])->assertOk();
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/conclude', ['conclusion' => 'Complete'])->assertOk();
    $this->postJson('/api/v1/accounting/workpapers/'.$workpaper->id.'/exports', ['format' => 'json'])->assertCreated();
});

it('requires workpaper abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/workpapers')->assertForbidden();
    $this->postJson('/api/v1/accounting/workpapers', [])->assertForbidden();
});

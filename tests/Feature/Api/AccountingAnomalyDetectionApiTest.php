<?php

declare(strict_types=1);
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\AnomalyDetection\Actions\RecordAnomaly;

uses(RefreshDatabase::class);
it('lists and sends tenant anomalies to review', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Anomaly Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    app(RecordAnomaly::class)->handle(['team_id' => $team->id, 'kind' => 'duplicate', 'title' => 'Possible duplicate payment', 'confidence' => '0.92']);
    Sanctum::actingAs($user, ['accounting.anomaly-detection.read', 'accounting.anomaly-detection.write']);
    $this->getJson('/api/v1/accounting/anomalies')->assertOk()->assertJsonPath('data.0.attributes.kind', 'duplicate');
    $id = DB::table('accounting_anomalies')->value('id');
    $this->postJson("/api/v1/accounting/anomalies/{$id}/send-to-review")->assertOk()->assertJsonPath('data.attributes.status', 'sent_to_review');
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('manages Xero connections and migration records through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.xero-migration.read', 'accounting.xero-migration.write']);

    $this->postJson('/api/v1/accounting/xero-migration/connections', [
        'tenant_ref' => 'tenant-1',
        'access_token' => 'secret-access-token',
    ])->assertCreated()->assertJsonMissingPath('access_token');

    $connection = XeroConnection::query()->firstOrFail();
    $this->getJson('/api/v1/accounting/xero-migration/connections')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonMissingPath('data.0.access_token');

    $this->postJson('/api/v1/accounting/xero-migration/connections/'.$connection->id.'/records', [
        'source_type' => 'invoice',
        'source_id' => 'xero-invoice-1',
    ])->assertCreated();

    expect($connection->migrationRecords()->count())->toBe(1);
});

it('requires Xero migration abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/xero-migration/connections')->assertForbidden();
    $this->postJson('/api/v1/accounting/xero-migration/connections', [])->assertForbidden();
});

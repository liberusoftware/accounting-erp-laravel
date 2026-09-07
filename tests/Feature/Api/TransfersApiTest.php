<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Transfers\Models\Transfer;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes transfers to the current team and supports lifecycle operations', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.transfers.read', 'accounting.transfers.write']);

    $response = $this->postJson('/api/v1/accounting/transfers', [
        'source_account_ref' => 'bank-ca',
        'destination_account_ref' => 'bank-us',
        'source_currency' => 'CAD',
        'destination_currency' => 'USD',
        'source_amount' => 100,
        'exchange_rate' => 0.74,
    ])->assertCreated();

    $transfer = Transfer::query()->firstOrFail();
    expect($transfer->team_id)->toBe($team->id);
    $this->getJson('/api/v1/accounting/transfers')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/transfers/'.$transfer->id.'/complete')->assertOk();
    $this->postJson('/api/v1/accounting/transfers/'.$transfer->id.'/reconcile', ['external_ref' => 'ref-1', 'amount' => 74, 'reconciled_on' => '2026-08-29'])->assertCreated();
    expect($response->json('status'))->toBe('in_transit');
});

it('requires transfer abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/transfers')->assertForbidden();
    $this->postJson('/api/v1/accounting/transfers', [])->assertForbidden();
});

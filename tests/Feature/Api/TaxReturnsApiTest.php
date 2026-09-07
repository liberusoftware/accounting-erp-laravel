<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\TaxReturns\Models\TaxReturn;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes tax returns and supports submission through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.tax-returns.read', 'accounting.tax-returns.write']);

    $this->postJson('/api/v1/accounting/tax-returns', ['tax_type' => 'vat', 'jurisdiction' => 'GB', 'period_start' => '2026-04-01', 'period_end' => '2026-06-30'])->assertCreated();
    $taxReturn = TaxReturn::query()->firstOrFail();
    $this->getJson('/api/v1/accounting/tax-returns')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/tax-returns/'.$taxReturn->id.'/submit', ['external_reference' => 'HMRC-1'])->assertOk();
});

it('requires tax return abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/tax-returns')->assertForbidden();
    $this->postJson('/api/v1/accounting/tax-returns', [])->assertForbidden();
});

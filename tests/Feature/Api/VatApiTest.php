<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Vat\Models\VatRecord;
use Liberu\Accounting\Vat\Models\VatReturn;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('manages VAT records and returns through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.vat.read', 'accounting.vat.write']);

    $this->postJson('/api/v1/accounting/vat/records', ['direction' => 'input', 'tax_code' => 'P20', 'net_amount' => 50, 'tax_amount' => 10, 'occurred_on' => '2026-08-01'])->assertCreated();
    $record = VatRecord::query()->firstOrFail();
    $this->postJson('/api/v1/accounting/vat/records/'.$record->id.'/digital-evidence', ['payload' => ['source' => 'bill-1']])->assertCreated();
    $this->postJson('/api/v1/accounting/vat/returns', ['period_start' => '2026-08-01', 'period_end' => '2026-08-31'])->assertCreated();
    $return = VatReturn::query()->firstOrFail();
    $this->postJson('/api/v1/accounting/vat/returns/'.$return->id.'/adjustments', ['box' => 4, 'amount' => 1, 'reason' => 'Rounding'])->assertCreated();
    $this->postJson('/api/v1/accounting/vat/returns/'.$return->id.'/submit', ['submission_ref' => 'vat-ref'])->assertOk();
});

it('requires VAT abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/vat/records')->assertForbidden();
    $this->postJson('/api/v1/accounting/vat/records', [])->assertForbidden();
});

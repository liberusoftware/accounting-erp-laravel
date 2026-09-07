<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Services\TeamManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('guides a newly registered team to setup before showing the app dashboard', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    app(TeamManagementService::class)->createPersonalTeamForUser($user);

    $this->actingAs($user)
        ->get('/app')
        ->assertRedirect('/app/account-setup');
});

it('keeps team setup credentials encrypted and hidden from the model cast', function (): void {
    $owner = User::factory()->create();
    $team = Team::forceCreate([
        'user_id' => $owner->id,
        'name' => 'Encrypted setup team',
        'personal_team' => true,
        'accounting_setup' => [
            'currency' => 'GBP',
            'integrations' => ['xero' => ['client_secret' => 'secret-value']],
        ],
    ]);

    expect($team->getRawOriginal('accounting_setup'))
        ->not->toContain('secret-value');
    expect($team->accounting_setup['integrations']['xero']['client_secret'])
        ->toBe('secret-value');
});

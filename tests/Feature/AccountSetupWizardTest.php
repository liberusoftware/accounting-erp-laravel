<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Liberu\Accounting\Erp\Http\Middleware\EnsureAccountingSetup;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TeamManagementService;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\AccountSetupWizard;
use Liberu\Accounting\ErpFilament\Filament\App\Widgets\AccountingQuickActions;
use Livewire\Livewire;

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

it('saves workspace preferences while preserving write-only credentials and other setup data', function (): void {
    $user = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($user);
    $team->update(['accounting_setup' => [
        'integrations' => ['xero' => ['client_secret' => 'keep-this-secret']],
        'opening_balances_imported' => true,
    ]]);
    $user->unsetRelation('currentTeam');
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant(null);

    Livewire::test(AccountSetupWizard::class)
        ->assertDontSee('keep-this-secret')
        ->fillForm([
            'business_name' => 'Acme Books',
            'country' => 'GB',
            'currency' => 'GBP',
            'fiscal_year_start' => '04-01',
            'timezone' => 'Europe/London',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect('/app');

    $team->refresh();
    expect($team->name)->toBe('Acme Books')
        ->and($team->accounting_setup['fiscal_year_start'])->toBe('04-01')
        ->and($team->accounting_setup['integrations']['xero']['client_secret'])->toBe('keep-this-secret')
        ->and($team->accounting_setup['opening_balances_imported'])->toBeTrue()
        ->and($team->accounting_setup_completed_at)->not->toBeNull();
});

it('rejects invalid timezones without completing setup', function (): void {
    $user = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($user);
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant(null);

    Livewire::test(AccountSetupWizard::class)
        ->fillForm(['timezone' => 'Invalid/Timezone'])
        ->call('save')
        ->assertHasFormErrors(['timezone']);

    expect($team->fresh()->accounting_setup_completed_at)->toBeNull();
});

it('prevents a team member from changing owner setup', function (): void {
    $owner = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($owner);
    $member = User::factory()->create(['current_team_id' => $team->id]);
    $team->users()->attach($member, ['role' => 'editor']);
    $this->actingAs($member);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant(null);

    expect(AccountSetupWizard::canAccess())->toBeFalse();
    Livewire::test(AccountSetupWizard::class)->assertForbidden();
});

it('does not force a member into a wizard they cannot edit', function (): void {
    $owner = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($owner);
    $member = User::factory()->create(['current_team_id' => $team->id]);
    $team->users()->attach($member, ['role' => 'editor']);
    $request = Request::create('/app');
    $request->setUserResolver(fn () => $member);

    $response = app(EnsureAccountingSetup::class)
        ->handle($request, fn () => response('Dashboard'));

    expect($response->getContent())->toBe('Dashboard');
});

it('shows saved accounting preferences on the workspace dashboard without exposing credentials', function (): void {
    $user = User::factory()->create();
    $team = app(TeamManagementService::class)->createPersonalTeamForUser($user);
    $team->update([
        'accounting_setup' => [
            'currency' => 'GBP',
            'fiscal_year_start' => '04-01',
            'integrations' => ['xero' => ['client_secret' => 'dashboard-secret']],
        ],
        'accounting_setup_completed_at' => now(),
    ]);
    $user->unsetRelation('currentTeam');
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant(null);

    $workspace = app(AccountingQuickActions::class)->workspace();

    expect($workspace)->toMatchArray([
        'currency' => 'GBP',
        'fiscal_year_start' => '1 April',
        'completed' => true,
    ])->not->toHaveKey('integrations');
});

<?php

declare(strict_types=1);

use App\Filament\Admin\Pages\ManageGeneralSettings;
use App\Filament\Admin\Pages\PlatformSetupWizard;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Facades\Filament;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\RolesPermissions\Models\Role;
use Livewire\Livewire;

it('restricts platform configuration to super administrators', function (): void {
    $this->actingAs(User::factory()->create());

    expect(PlatformSetupWizard::canAccess())->toBeFalse()
        ->and(ManageGeneralSettings::canAccess())->toBeFalse();
});

it('lets a super administrator configure the platform without changing optional settings', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    setPermissionsTeamId($team->id);
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    $user->assignRole('super_admin');
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::setTenant($team);
    $settings = app(GeneralSettings::class);
    $settings->github_url = 'https://github.com/example';
    $settings->save();

    Livewire::test(PlatformSetupWizard::class)
        ->fillForm([
            'site_name' => 'Acme Accounting',
            'site_email' => 'support@example.com',
            'site_country' => 'United Kingdom',
            'site_currency' => '£',
            'site_default_language' => 'en',
            'footer_copyright' => 'Acme Accounting',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();
    expect($settings->site_name)->toBe('Acme Accounting')
        ->and($settings->site_email)->toBe('support@example.com')
        ->and($settings->github_url)->toBe('https://github.com/example');
});

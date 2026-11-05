<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Settings\GeneralSettings;
use Liberu\Accounting\ErpFilament\Filament\Admin\Pages\ManageGeneralSettings;
use Liberu\Accounting\ErpFilament\Filament\Admin\Pages\PlatformSetupWizard;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\RolesPermissions\Models\Role;
use Livewire\Livewire;
use Spatie\LaravelSettings\Factories\SettingsRepositoryFactory;

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

it('preserves legacy ERP settings when moving to the shared site settings', function () {
    $repository = SettingsRepositoryFactory::create();
    $repository->createProperty('general', 'site_name', 'Existing ERP name', true);
    $repository->createProperty('general', 'github_url', 'https://github.com/existing');

    $files = glob(base_path('modules/accounting-erp/database/migrations/*migrate_legacy_erp_general_settings.php'));
    (require $files[0])->up();

    expect($repository->getPropertyPayload('site', 'site_name'))->toBe('Existing ERP name')
        ->and($repository->getPropertyPayload('site', 'github_url'))->toBe('https://github.com/existing')
        ->and($repository->getPropertyPayload('general', 'site_name'))->toBe('Existing ERP name')
        ->and($repository->getLockedProperties('site'))->toContain('site_name');
});

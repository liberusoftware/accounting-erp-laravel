<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Models\UserNotificationPreference;
use Liberu\Accounting\Erp\Services\TeamManagementService;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\NotificationSettings;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\TeamNotificationSettings;
use Tests\TestCase;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_team_vonage_credentials_persists_them_encrypted(): void
    {
        [$user, $team] = $this->ownerWithTeam();
        $this->actingAs($user);
        $this->useAppPanelTenant($team);

        $page = new TeamNotificationSettings();
        $page->data = [
            'vonage_key' => 'pubkey123',
            'vonage_secret' => 'secret-xyz',
            'vonage_from' => 'Acme',
        ];
        $page->save();

        $fresh = Team::findOrFail($team->id);
        // Decrypted via the model's `encrypted` cast.
        $this->assertSame('pubkey123', $fresh->vonage_key);
        $this->assertSame('secret-xyz', $fresh->vonage_secret);
        $this->assertSame('Acme', $fresh->vonage_from);

        // Raw column is ciphertext, not the plaintext.
        $rawKey = DB::table('teams')->where('id', $team->id)->value('vonage_key');
        $this->assertNotNull($rawKey);
        $this->assertNotSame('pubkey123', $rawKey);
    }

    public function test_blank_secret_fields_keep_the_existing_credentials(): void
    {
        [$user, $team] = $this->ownerWithTeam();
        $this->actingAs($user);
        $this->useAppPanelTenant($team);

        $team->forceFill(['vonage_key' => 'keep-key', 'vonage_secret' => 'keep-secret'])->save();

        $page = new TeamNotificationSettings();
        $page->data = ['vonage_key' => '', 'vonage_secret' => '', 'vonage_from' => 'NewSender'];
        $page->save();

        $fresh = Team::findOrFail($team->id);
        $this->assertSame('keep-key', $fresh->vonage_key);
        $this->assertSame('keep-secret', $fresh->vonage_secret);
        $this->assertSame('NewSender', $fresh->vonage_from);
    }

    public function test_team_settings_page_is_gated_to_the_team_owner(): void
    {
        [$owner, $team] = $this->ownerWithTeam();
        $this->actingAs($owner);
        $this->useAppPanelTenant($team);

        $this->assertTrue(TeamNotificationSettings::canAccess());

        $stranger = User::factory()->create();
        $this->actingAs($stranger->fresh());
        $this->assertFalse(TeamNotificationSettings::canAccess());
    }

    public function test_saving_personal_preferences_upserts_a_single_row(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $page = new NotificationSettings();
        $page->data = [
            'phone' => '+15551234567',
            'mail_enabled' => true,
            'database_enabled' => false,
            'sms_enabled' => true,
        ];
        $page->save();

        $this->assertDatabaseCount('user_notification_preferences', 1);
        $pref = UserNotificationPreference::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('+15551234567', $pref->phone);
        $this->assertTrue($pref->mail_enabled);
        $this->assertFalse($pref->database_enabled);
        $this->assertTrue($pref->sms_enabled);

        // Second save updates the same row instead of inserting another.
        $update = new NotificationSettings();
        $update->data = [
            'phone' => '+19998887777',
            'mail_enabled' => false,
            'database_enabled' => true,
            'sms_enabled' => false,
        ];
        $update->save();

        $this->assertDatabaseCount('user_notification_preferences', 1);
        $pref->refresh();
        $this->assertSame('+19998887777', $pref->phone);
        $this->assertFalse($pref->mail_enabled);
        $this->assertTrue($pref->database_enabled);
        $this->assertFalse($pref->sms_enabled);
    }

    /** @return array{0: User, 1: Team} */
    private function ownerWithTeam(): array
    {
        $user = User::factory()->create();
        $team = app(TeamManagementService::class)->createPersonalTeamForUser($user);

        return [$user->fresh(), $team];
    }

    private function useAppPanelTenant(Team $team): void
    {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($team);
    }
}

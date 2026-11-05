<?php

declare(strict_types=1);

namespace Tests\Feature\Provisioning;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Tests\TestCase;

class ProvisionTenantChartCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_provisions_the_team_by_id(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);

        $this->artisan('tenants:provision-chart', ['team' => $team->id])->assertSuccessful();

        $this->assertSame(18, Account::where('team_id', $team->id)->count());
    }

    public function test_command_fails_cleanly_for_an_unknown_team(): void
    {
        $this->artisan('tenants:provision-chart', ['team' => 999999])->assertFailed();

        $this->assertSame(0, Account::count());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Provisioning;

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\Pages\ListChartOfAccounts;
use Livewire\Livewire;
use Tests\TestCase;

class ProvisionChartActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_action_provisions_the_current_tenant(): void
    {
        $user = User::factory()->create();
        $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
        $user->teams()->attach($team); // membership so the panel accepts the tenant
        $user->forceFill(['current_team_id' => $team->id])->save();

        $this->actingAs($user);
        Filament::setTenant($team);

        Livewire::test(ListChartOfAccounts::class)
            ->callAction('provisionChart');

        $this->assertSame(18, Account::where('team_id', $team->id)->count());
    }
}

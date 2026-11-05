<?php

declare(strict_types=1);

namespace Tests\Feature\Provisioning;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;
use Tests\TestCase;

class TenantProvisioningServiceTest extends TestCase
{
    use RefreshDatabase;

    private function team(): Team
    {
        $user = User::factory()->create();

        return Team::forceCreate(['user_id' => $user->id, 'name' => 'Acme', 'personal_team' => false]);
    }

    public function test_provisions_the_standard_chart_with_correct_types_and_normal_balance(): void
    {
        $team = $this->team();

        $count = app(TenantProvisioningService::class)->provisionChartOfAccounts($team);

        $this->assertSame(18, $count);
        $accounts = Account::where('team_id', $team->id)->get();
        $this->assertCount(18, $accounts);

        // key accounts the app's GL features depend on, with the right type + derived normal_balance
        $ar = $accounts->firstWhere('account_number', 1100);
        $this->assertSame('Accounts Receivable', $ar->account_name);
        $this->assertSame('asset', $ar->account_type);
        $this->assertSame('debit', $ar->normal_balance);

        $deferred = $accounts->firstWhere('account_number', 2400);
        $this->assertSame('Deferred Revenue', $deferred->account_name);
        $this->assertSame('liability', $deferred->account_type);
        $this->assertSame('credit', $deferred->normal_balance);

        $sales = $accounts->firstWhere('account_number', 4000);
        $this->assertSame('revenue', $sales->account_type);
        $this->assertSame('credit', $sales->normal_balance);

        // every row stamped with the team + owner (not a default team, not null)
        $this->assertTrue($accounts->every(fn (Account $a): bool => (int) $a->team_id === $team->id));
        $this->assertTrue($accounts->every(fn (Account $a): bool => (int) $a->user_id === $team->user_id));
    }

    public function test_is_idempotent_when_the_team_already_has_accounts(): void
    {
        $team = $this->team();
        app(TenantProvisioningService::class)->provisionChartOfAccounts($team);

        $again = app(TenantProvisioningService::class)->provisionChartOfAccounts($team->fresh());

        $this->assertSame(0, $again);
        $this->assertSame(18, Account::where('team_id', $team->id)->count());
    }
}

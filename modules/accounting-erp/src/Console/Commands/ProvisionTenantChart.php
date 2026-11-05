<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Console\Commands;

use Illuminate\Console\Command;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Services\TenantProvisioningService;

class ProvisionTenantChart extends Command
{
    #[\Override]
    protected $signature = 'tenants:provision-chart {team : Team ID}';

    #[\Override]
    protected $description = 'Provision a standard chart of accounts for a team';

    public function handle(TenantProvisioningService $service): int
    {
        $team = Team::find($this->argument('team'));
        if (! $team instanceof Team) {
            $this->error("Team {$this->argument('team')} not found.");

            return self::FAILURE;
        }

        $count = $service->provisionChartOfAccounts($team);
        $this->info($count > 0
            ? "Provisioned {$count} accounts for team {$team->id}."
            : "Team {$team->id} already has accounts; skipped.");

        return self::SUCCESS;
    }
}

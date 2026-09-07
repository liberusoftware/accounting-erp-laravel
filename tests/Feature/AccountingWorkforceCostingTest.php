<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\WorkforceCosting\Actions\AllocateWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Actions\CapitalizeWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Actions\CreateWorkforceCostingRule;
use Liberu\Accounting\WorkforceCosting\Actions\RecordWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceAllocationType;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceCostStatus;
use Liberu\Accounting\WorkforceCosting\Exceptions\InvalidWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Queries\WorkforceProfitability;

uses(RefreshDatabase::class);

it('records allocates capitalizes and reports workforce cost', function (): void {
    $cost = app(RecordWorkforceCost::class)->handle(['team_id' => 17, 'worker_ref' => 'employee-1', 'cost_date' => '2026-08-01', 'hours' => 8, 'hourly_rate' => 25]);
    app(CreateWorkforceCostingRule::class)->handle(['team_id' => 17, 'name' => 'Project delivery', 'allocation_type' => WorkforceAllocationType::Project]);
    $allocated = app(AllocateWorkforceCost::class)->handle($cost, WorkforceAllocationType::Project, 'project-12');
    $capitalized = app(CapitalizeWorkforceCost::class)->handle($allocated);

    expect($capitalized->amount)->toBe('200.000000')
        ->and($capitalized->status)->toBe(WorkforceCostStatus::Posted)
        ->and(app(WorkforceProfitability::class)->handle(17)['project:project-12']['capitalized'])->toBe('200');
});

it('rejects invalid workforce allocations', function (): void {
    expect(fn (): mixed => app(RecordWorkforceCost::class)->handle(['team_id' => 17, 'worker_ref' => 'employee-1', 'cost_date' => '2026-08-01', 'amount' => -1]))
        ->toThrow(InvalidWorkforceCost::class);
});

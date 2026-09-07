<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Dashboards\Actions\CreateDashboard;
use Liberu\Accounting\Dashboards\Actions\ShareDashboard;
use Liberu\Accounting\Dashboards\Actions\UpsertKpi;
use Liberu\Accounting\Dashboards\Queries\DashboardQuery;

uses(RefreshDatabase::class);

it('creates a dashboard, refreshes a KPI and shares it', function (): void {
    $dashboard = app(CreateDashboard::class)->handle(['team_id' => 31, 'dashboard_ref' => 'EXEC', 'name' => 'Executive', 'role' => 'director', 'period' => '2026-Q3', 'dimensions' => ['region' => 'UK']]);
    $kpi = app(UpsertKpi::class)->handle($dashboard, ['kpi_ref' => 'revenue', 'label' => 'Revenue', 'value' => 125000, 'target' => 120000, 'unit' => 'GBP', 'drill_through' => ['route' => 'sales.index']]);
    $share = app(ShareDashboard::class)->handle($dashboard, ['shared_with_role' => 'board']);

    expect((float) $kpi->value)->toBe(125000.0)->and($share->token)->toHaveLength(48)->and(app(DashboardQuery::class)->forTeam(31)->first()->kpis)->toHaveCount(1);
});

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\KpiAndGoals\Actions\AddCommentary;
use Liberu\Accounting\KpiAndGoals\Actions\CreateGoal;
use Liberu\Accounting\KpiAndGoals\Actions\CreateMetric;
use Liberu\Accounting\KpiAndGoals\Actions\RecordMeasurement;
use Liberu\Accounting\KpiAndGoals\Enums\GoalStatus;
use Liberu\Accounting\KpiAndGoals\Exceptions\InvalidKpi;
use Liberu\Accounting\KpiAndGoals\Queries\KpiQuery;

uses(RefreshDatabase::class);

it('governs metrics, tracks goal progress, triggers alerts, and stores commentary', function (): void {
    $metric = app(CreateMetric::class)->handle(['team_id' => 1, 'metric_ref' => 'gross-margin', 'name' => 'Gross margin', 'unit' => 'percent', 'source_contract' => 'ledger.margin']);
    $goal = app(CreateGoal::class)->handle($metric, ['team_id' => 1, 'goal_ref' => 'GM-2026', 'name' => 'Improve margin', 'owner_ref' => 'cfo-1', 'period_start' => '2026-01-01', 'period_end' => '2026-12-31', 'baseline' => 20, 'target' => 30, 'warning_threshold' => 24, 'critical_threshold' => 15]);
    $measurement = app(RecordMeasurement::class)->handle($goal, ['period_ref' => '2026-Q1', 'measured_on' => '2026-03-31', 'value' => 25, 'source_ref' => 'ledger-run-1']);
    $comment = app(AddCommentary::class)->handle($goal, 'cfo-1', 'Margin improved after pricing review.', '2026-Q1');
    expect($measurement->progress)->toEqual('0.500000')->and($goal->fresh()->status)->toBe(GoalStatus::Active)->and($comment->body)->toContain('pricing')->and(app(KpiQuery::class)->progress($goal)['open_alerts'])->toBe(0);
});

it('rejects invalid metrics, periods, and non-finite measurements', function (): void {
    expect(fn (): mixed => app(CreateMetric::class)->handle(['metric_ref' => 'bad', 'name' => 'Bad', 'unit' => 'x', 'source_contract' => 'contract', 'direction' => 'sideways']))->toThrow(InvalidKpi::class);
    $metric = app(CreateMetric::class)->handle(['metric_ref' => 'm', 'name' => 'Metric', 'unit' => 'count', 'source_contract' => 'contract']);
    expect(fn (): mixed => app(CreateGoal::class)->handle($metric, ['goal_ref' => 'bad', 'name' => 'Bad', 'owner_ref' => 'owner', 'period_start' => '2026-03-01', 'period_end' => '2026-01-01', 'target' => 1]))->toThrow(InvalidKpi::class);
});

it('exposes authenticated KPI API routes', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['accounting.kpi-and-goals.read', 'accounting.kpi-and-goals.write']);
    $this->postJson('/api/v1/accounting/kpi-and-goals/metrics', ['team_id' => 1, 'metric_ref' => 'api-metric', 'name' => 'API metric', 'unit' => 'count', 'source_contract' => 'api.contract'])->assertCreated();
    $this->getJson('/api/v1/accounting/kpi-and-goals/goals')->assertOk();
});
